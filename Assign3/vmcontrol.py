from time import time
from subprocess import Popen, PIPE
from threading import Timer

logfile = open('/home/ubuntu/tweetlog.data', 'w')

starttime = 0.0
runstarttime = 0.0
starttweetcount = 0
skew = 0.0

def tick():
	global skew
	global iteration
	global starttime
	global starttweetcount

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
		logfile.write("%d\t%f\t%d\t%d\t%f\n" % (int(runtime), runtime, tweetcount, tweetdiff, (tweetdiff / timediff)));
		logfile.flush();
		print("Done %d; skew %f" % (runtime, skew))

	starttweetcount = tweetcount
	starttime = time()


runstarttime = time()
tick()
