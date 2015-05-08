from time import time
from subprocess import Popen, PIPE
from threading import Timer
from ec2lib import *
import os

# global configuration variables
budget = int(os.environ["BUDGET"]) # budget in euros
savetydiff = 0.5 # safety amount in euros
tps = int(os.environ["TPS"]) # tweets per second
max_strikelow = 5 # max strikes for low performance
max_strikehigh = 5 # max strikes for high performance
vmstartuptime = 120 # estimated time to start VM
ec2 = EC2Lib("eu-west-1", os.environ["ACCESSK"], os.environ["SECRETK"])

# global variables
logfile = open('/home/ubuntu/tweetlog.data', 'w')
starttime = 0.0
runstarttime = 0.0
starttweetcount = 0
skew = 0.0
strikelow = 0
strikehigh = 0
moneyleft = True
vmloading = False
# [typename, number of instances, max instances]
vmtype1 = ["t2.micro", 0, 20]
vmtype2 = ["t2.small", 0, 20]


def tick():
	global skew
	global iteration
	global starttime
	global starttweetcount
	global strikelow
	global strikehigh
	global vmloading
	global ec2
	global vmtype
	global vmstartuptime
	global savetydiff
	global moneyleft
	global vmtype1
	global vmtype2

	if(starttime != 0.0):
		endtime = time()
		timediff = endtime - starttime
		runtime = endtime - runstarttime
		skew = runtime - int(runtime)

	# Partial source http://stackoverflow.com/a/2101458
	p1 = Popen(["cat", "/root/tweetstats.data"], stdout=PIPE)
	p2 = Popen(["awk", "{sum+=$3+$4} END {print sum}"], stdin=p1.stdout, stdout=PIPE)
	try:
		tweetcount = int(p2.communicate()[0])
	except ValueError:
		tweetcount = 0

	if(starttime != 0.0):
		tweetdiff = tweetcount - starttweetcount
		
		if(ec2.get_total_costs() > (budget - savetydiff)):
			ec2.stop_all()
			moneyleft = False
		
		cur_tps = (tweetdiff / timediff)
		if (cur_tps < tps and moneyleft and not vmloading and tweetcount > 0):
			strikelow = strikelow + 1;
			print("Performance too low. strike: %d" % strikelow)
			if(strikelow == max_strikelow):
				print("%d strikes! Starting new VM.." % max_strikelow)
				if(vmtype1[1] < vmtype1[2]):
					vmtype = vmtype1[0]
					vmtype1[1] = vmtype1[1] + 1
				elif(vmtype2[1] < vmtype2[2]):
					vmtype = vmtype2[0]
					vmtype2[1] = vmtype2[1] + 1
				else:
					print("maximum number of instances reached!");
					vmtype = ""
					
				if(vmtype != ""):
					ec2.start_instance("ami-dcdfcbb4",vmtype)
					vmloading = True
					Timer(vmstartuptime, vmloaded).start()
					strikelow = 0
		else:
			strikelow = 0

		if (cur_tps > tps*1.5):
			strikehigh = strikehigh + 1;
			print("Performance too high. strike: %d" % strikehigh)
			if(strikehigh == max_strikehigh):
				print("%d strikes! Stopping last VM.." % max_strikehigh)
				strikehigh = 0;
				if(vmtype2[1] != 0):
					ec2.stop_last()
					vmtype2[1] = vmtype2[1] - 1
				elif(vmtype1[1] != 0):
					ec2.stop_last()
					vmtype1[1] = vmtype1[1] - 1
				else:
					print("no instances running to stop..")
		else:
			strikehigh = 0
		
		logfile.write("%d\t%f\t%d\t%d\t%f\t%d\t%d\t%d\t%d\t%f\n" % (int(runtime), runtime, tweetcount, tweetdiff, cur_tps, strikelow, strikehigh, vmtype1[1], vmtype2[1], ec2.get_total_costs()));
		logfile.flush();


		print("tweetcount: %d" % tweetcount)
		print("tweets per second: %f" % cur_tps)
		print("costs: %f" % ec2.get_total_costs())
		print("Done %d; skew %f" % (runtime, skew))
		
		if (tweetcount > 0 and cur_tps == 0 ):
			print("Done! (or something is wrong with pumpkin..)")
			ec2.stop_all()
			return

	
	# Last cycle took skew seconds more than specified, adjust for the next cycle.
	Timer(10 - skew, tick).start()

	starttweetcount = tweetcount
	starttime = time()

def vmloaded():
	global vmloading
	vmloading = False
				

runstarttime = time()
tick()
