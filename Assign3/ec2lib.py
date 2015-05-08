import boto
import boto.ec2
import base64
import math

from threading import Timer
from time import time
from ec2pricing import *

class EC2Lib:
	#base64.b64encode(
	RUN_CODE = """#!/bin/bash -xe
	sh /home/ubuntu/start.sh
	"""

	conn = None
	instances = []
	prices = {}

	def __init__(self, region, access_key, secret):
		self.conn = boto.ec2.connect_to_region(region, aws_access_key_id=access_key, aws_secret_access_key=secret)
		self.prices = get_ec2_pricing(region)

	def start_instance(self, ami, instance_type):
		reservation = self.conn.run_instances(image_id = ami,
                                     key_name='group07_2',
                                     instance_type=instance_type,
                                     security_group_ids=['sg-b72273d3'],
                                     subnet_id='subnet-c7585381',
                                     instance_initiated_shutdown_behavior='terminate',
                                     user_data=self.RUN_CODE)

		
		for instance in reservation.instances:
			instance.add_tag('InstanceOwner', 'ccengstud07')
			self.instances.append(EC2Instance(instance, self.prices[instance_type]))

		return reservation

	def get_security_groups(self):
		return self.conn.get_all_security_groups();

	def get_key_pairs(self):
		return self.conn.get_all_key_pairs();

	def get_total_costs(self):
		costs = 0
		for instance in self.instances:
			costs = costs + instance.run_cost()
			
		return costs

	def stop_last(self):
		instances[-1].stop(0)
			
	def stop_all(self):
		for instance in self.instances:
			instance.stop(0)
		

class EC2Instance:
	start_time = 0
	stop_scheduled = False
	instance = None
	price = 0.0

	def __init__(self, instance, price):
		self.instance = instance
		self.price = price
		self.start_time = time()

	def run_time(self):
		return time() - self.start_time;

	def run_cost(self):
		return math.ceil(self.run_time() / 3600.0) * self.price

	def stop(self, wait_time):
		if(self.instance != None):
			print "Schedule termination of %s after %d seconds" % (self.instance.id, wait_time)
			stop_scheduled = True
			Timer(wait_time, self.terminate_self).start()

	def terminate_self(self):
		if(self.instance != None):
			print "Terminating %s" % self.instance.id
			self.instance.stop(True)
			self.instance.terminate()
			self.instance = None
	

