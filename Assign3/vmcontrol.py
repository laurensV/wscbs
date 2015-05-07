from time import time
from subprocess import Popen, PIPE
from threading import Timer
from ec2lib import *

budget = 5.0
savetydiff = 0.5
tps = 1000
max_strike = 5
vmtype = "t2.micro"
vmstartuptime = 100

logfile = open('/home/ubuntu/tweetlog.data', 'w')

starttime = 0.0
runstarttime = 0.0
starttweetcount = 0
skew = 0.0
strike = 0
moneyleft = True

vmloading = False
ec2 = EC2Lib("us-east-1", "ACCESS_KEY", "SECRET_KEY")


def tick():
	global skew
	global iteration
	global starttime
	global starttweetcount
	global strike
	global vmloading
	global ec2
	global vmtype
	global vmstartuptime
	global savetydiff
	global moneyleft

	if(starttime != 0.0):
		endtime = time()
		timediff = endtime - starttime
		runtime = endtime - runstarttime
		skew = runtime - int(runtime)

	# Last cycle took skew seconds more than specified, adjust for the next cycle.
	Timer(10 - skew, tick).start()

	# Partial source http://stackoverflow.com/a/2101458
	p1 = Popen(["cat", "/root/tweetstats.data"], stdout=PIPE)
	p2 = Popen(["awk", "{sum+=$3+$4} END {print sum}"], stdin=p1.stdout, stdout=PIPE)
	try:
		tweetcount = int(p2.communicate()[0])
	except ValueError:
		tweetcount = 0

	if(starttime != 0.0):
		tweetdiff = tweetcount - starttweetcount
		
		if(ec2.get_total_costs() > (budget + savetydiff)):
			ec2.stop_all()
			moneyleft = False

		if ((tweetdiff / timediff) < tps and moneyleft and not vmloading):
			strike = strike + 1;
			print("Performance too low. Strike: %d" % strike)
			if(strike == max_strike):
				print("%d Strikes! Starting new VM.." % max_strike)
				ec2.start_instance("ami-00756068",vmtype)
				vmloading = True
				Timer(vmstartuptime, vmloaded).start()
				strike = 0
				
		logfile.write("%d\t%f\t%d\t%d\t%f\n" % (int(runtime), runtime, tweetcount, tweetdiff, (tweetdiff / timediff)));
		logfile.flush();



		print("Done %d; skew %f" % (runtime, skew))

	starttweetcount = tweetcount
	starttime = time()

def vmloaded():
	global vmloading
	vmloading = False
				

runstarttime = time()
tick()
