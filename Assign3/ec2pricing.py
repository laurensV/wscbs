import re, string, urllib2, json

def get_ec2_pricing(region_code):
	url = 'http://a0.awsstatic.com/pricing/1/ec2/linux-od.min.js'
	response = urllib2.urlopen(url);
	respdata = response.read()
	idx = string.find(respdata, 'callback(');

	table = {}

	# Strip javascript.
	respdata = respdata[(idx + 9):(len(respdata) - 2)]
	# Transform lazy JSON into proper JSON, that we can read.
	# {key: value} --> {"key": value}
	respdata = re.sub(r"(\w+):", r'"\1":', respdata)
	data = json.loads(respdata)
	for region in data["config"]["regions"]:
		if(region["region"] == region_code):
			for gen in region["instanceTypes"]:
				for instance in gen["sizes"]:
					table[instance["size"]] = float(instance["valueColumns"][0]["prices"]["USD"])

	return table

