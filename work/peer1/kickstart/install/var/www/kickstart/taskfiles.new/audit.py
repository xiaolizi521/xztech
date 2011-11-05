#!/usr/bin/python
# A python script to audit information using lshw, then using various
# tools, discover and insert missing information, such as RAID drives, 
# BBUs, etc.

# Import our module objects
import commands
import os
import re
import stat
import sys
import tempfile
import urllib
import xml.dom.minidom

__author__ = "Jim Bair"
__date__ = "2009-10-21"

# Set to true if being developed.
debug = False

# Global Vars
tmpdir = tempfile.gettempdir()
# 3ware cli util
tw_cli = tmpdir + '/tw_cli'
# LSI cli util
megaCli = tmpdir + '/MegaCli'
# File used during debugging to speed things up
lshwFile = 'lshw.txt'

# Types of nodes found in minidom
types =	[
	'ELEMENT_NODE',
	'ATTRIBUTE_NODE',
	'TEXT_NODE',
	'CDATA_SECTION_NODE',
	'ENTITY_NODE',
	'PROCESSING_INSTRUCTION_NODE',
	'COMMENT_NODE',
	'DOCUMENT_NODE',
	'DOCUMENT_TYPE_NODE',
	'NOTATION_NODE'
	]

# Classes that lshw generates
# lshw -short | sed -n ' 3,$ p ' | sed 's/^.\{1,37\}//' | awk '{print $1}' \
# | sort | uniq 
classes = [
	'bridge',
	'bus',
	'disk',
	'display',
	'memory',
	'network',
	'processor',
	'storage',
	'system'
	]

##################################################
## Global Data Handling / System Type Functions ##
##################################################

def echo(message=''):
	"""
	Function to emulate echo -en '' type behavior. Mainly to avoid
	continuously typing sys.stdout.write/flush. Done since print 
	tends to just act "weird" and is changing in Pyton 3000. For 
	advanced %s type stuff, avoid this function.
	"""
	if message is not None:
		sys.stdout.write(message)
		sys.stdout.flush()
	else:
		return None


def findLine(pattern='', list=[]):
	"""
	Function to search for a regex-compatible string, grep style
	Only returns first result as string
	"""
	# The line we're looking for, case insensitive
	ourPattern = re.compile(pattern, re.IGNORECASE)

	# Check each line for our pattern
	for line in list:
		line.strip()
		ourResult = ourPattern.search(line)

		# Found it! Return it and exit out.
		if ourResult is not None:
			return line

	# If we get here, we found nothing above. Return None.
	return None


def findLines(pattern='', list=[]):
	"""
	Function to search for a regex-compatible string, grep style
	Returns a list of lines.
	"""
	results = []
	# The line we're looking for, case insensitive
	ourPattern = re.compile(pattern, re.IGNORECASE)

	# Check each line for our pattern
	for line in list:
		line.strip()
		ourResult = ourPattern.search(line)

	# Found it! Return it and mark it as non-empty
		if ourResult is not None:
			results.append(line)

	# Return what we found, if anything,
	if len(results) == 0:
		return None

	return results


def verifyRegex(pattern='', string=''):
	"""
	Function to run against a string to verify it matches in regex
	Used to verify if output from a command is what's expected
	"""
	# Compile our pattern and run against the string
	ourPattern = re.compile(pattern, re.IGNORECASE)
	ourResult = ourPattern.search(string)
	if ourResult is not None:
		return string
	else:
		return None


def runCommand(command=''):
	"""
	Wrapper to run system commands and verify a clean exit.
	"""

	runExit, runOut = commands.getstatusoutput(command)

	# Check if command did not exit gracefully
	if runExit != 0:
		sys.stderr.write('ERROR: "' + command + '" did not exit gracefully.\n\n')
		sys.stderr.write('Exit code: ' + str(runExit) + '\nFailed output:\n\n')
		sys.stderr.write(runOut)
		sys.exit(runExit)
	# Return our output
	return runOut


def checkForRoot():
	"""
	Ensure the script is being run by user root / uid 0
	"""
	if os.getuid() != 0:
                sys.stderr.write("This script must be run as root. Exiting.\n")
                sys.exit(1)


def stringToList(string=''):
	"""
	Turns a string with new lines into a list similar to readlines()
	"""
	list = []
	# Split into a list based on newlines
	for line in string.split('\n'):
		line = line.strip()
		list.append(line)
	# Remove any blank items
	if list.count('') is not 0:
		list.remove('')

	return list


def mergeListsIntoDict(list1=[], list2=[]):
	"""
	Used to take any 2 provided lists, use zip() to turn them
	into a list of tuples, then use dict() to turn them into 
	a dictionary. Returns the dictionary by itself.
	"""
	# Make sure we at least got something.
	if len(list1) == 0 and len(list2) == 0:
		result = None
	# Make sure the lists line up, otherwise, things will break or
	# give us unexpected results
	elif len(list1) != len(list2):
		sys.stderr.write("ERROR: mergeListsIntoDict Failed!\n")
		sys.stderr.write("The number of items in your lists did not match.\n")
		sys.stderr.write("Length of list1: " + str(len(list1)) + '\n')
		sys.stderr.write("Length of list2: " + str(len(list2)) + '\n')
		sys.stderr.write(str(list1) + '\n')
		sys.stderr.write(str(list2) + '\n')
		sys.exit(1)
	# Validation passed, build dictionary and return it.
	else:
		result = dict(zip(list1, list2))

	return result


def regexSplit(regex='', string=''):
	"""
	Function to print the results of a regex in a list, similar 
	to doing .split(). Takes in a string and returns a single list.
	Used to .split() based in regex.
	"""
	ourResult = re.search(regex, string)
	if ourResult is not None:
		ourList = list(ourResult.groups())
		return ourList
	else:
		return None

def findDisks():
	"""
	Finds all /dev/[h|s]d[a-z] disks and returns
	a list of the disks with their full paths.
	"""
	results = []
	rootFolder = '/dev/'
	for i in os.listdir(rootFolder):
		# If we have hdX or sdX, we're good
		if i.startswith('sd') or i.startswith('hd'):
			pass
		else:
			continue
		# Actual disk names are only 3 chars
		if len(i) is not 3:
			continue
		# Make sure it's a block device
		statbuf = os.stat(rootFolder + i)
		if not stat.S_ISBLK(statbuf.st_mode):
			continue
		# Make the full name and append to our list
		disk = rootFolder + i
		results.append(disk)

	return results

def hdparmFeatures(disk=''):
	"""
	Function to poll a disk and find the Commands/features section
	and return a dictionary with each feature labeled Enabled/Disabled
	"""
	flippedList = []
	
	ourRegex = re.compile(r"""^\t\s{3,}(\s|\*)\t(.*)$""", re.MULTILINE)
	ourData = runCommand('hdparm -I ' + disk) # work around
	
	if ourData is None:
		return None

	regexOutput = ourRegex.findall(ourData)
	if regexOutput is None:
		return None
 
	# Flip the values
	for i in regexOutput:
		# Every valid object should have 2 items
		if len(i) is not 2:
			continue
		id = i[1]
		# If value is *, that means it's enabled.
		if i[0] == '*':
			value = 'enabled'
		elif i[0] == ' ':
			value = 'disabled'
		flippedList.append((id, value))
 
	# Build and return our dict
	results = dict(flippedList)
	
	return results


#########################
## XML Based Functions ##
#########################

def lshw():
	"""
	Runs 'lshw -xml' and returns the parsed value. Reads lshwFile if debug = True
	Parsed with xml.dom.minidom
	"""

	# If we're in debug mode, read from a local file to speed things up.
	if debug:
		# Make sure the file is there.
		if os.path.isfile(lshwFile):
			echo('\nParsing ' + lshwFile + ' file...')
			result = xml.dom.minidom.parse(lshwFile)
			echo('done!\n\n')
		# If not, store to the text file and pasre it
		else:
			echo('\nStoring output of lshw to ' + lshwFile + '...')
			runCommand('lshw -xml > ' + lshwFile)
			echo('done!\n\nParsing ' + lshwFile + ' file...')
			result = xml.dom.minidom.parse(lshwFile)
			echo('done!\n\n')
	# If not in debug, run lshw directly.
	else:
		# Write to stderr to avoid stdout redirect from end result
		sys.stderr.write("\nFetching info from lshw...")
		lshwRun = runCommand('lshw -xml')
		# Bug fix per Travis.
		lshwRun = lshwRun.replace('&quot;', '"')

		# All is well. Parse our output.
		sys.stderr.write("done!\n\n")
		result = xml.dom.minidom.parseString(lshwRun)

	# Return our parsed XML DOM object
	return result

def getNodeType(node):
	"""
	Function used to discover the XML node type you are working with.
	"""
	type = ''
	if node.nodeType == dom.ELEMENT_NODE:
		type = "ELEMENT_NODE"
	if node.nodeType == dom.ATTRIBUTE_NODE:
		type = "ATTRIBUTE_TYPE"
	if node.nodeType == dom.TEXT_NODE:
		type = "TEXT_NODE"
	if node.nodeType == dom.CDATA_SECTION_NODE:
		type = "CDATA_SECTION_NODE"
	if node.nodeType == dom.ENTITY_NODE:
		type = "ENTITY_NODE"
	if node.nodeType == dom.PROCESSING_INSTRUCTION_NODE:
		type = "PROCESSING_INSTRUCTION_NODE"
	if node.nodeType == dom.COMMENT_NODE:
		type = "COMMENT_NODE"
	if node.nodeType == dom.DOCUMENT_NODE:
		type = "DOCUMENT_NODE"
	if node.nodeType == dom.DOCUMENT_TYPE_NODE:
		type = "DOCUMENT_TYPE_NODE"
	if node.nodeType == dom.NOTATION_NODE:
		type = "NOTATION_NODE"

	return type

def getNodes(name):
	"""
	Search for all descendants of global DOCUMENT_NODE object with a
	particular element type name. Returns a NodeList object.
	"""
	return dom.getElementsByTagName(name)

def getNodeAttribute(node, name):
	"""
	Search through all attributes of a Node object for a particular
	Attr.localName. Returns an Attr object.
	"""
	if node is None:
		return None
	if name is None:
		return None
	if not node.nodeType == node.ELEMENT_NODE:
		print >> sys.stdout, "Node is not of type ELEMENT_NODE"
		return None

	attribute = None
	if node.hasAttributes():
		for i in range(node.attributes.length):
			attr = node.attributes.item(i)
			if attr.name == name or attr.localName == name:
				attribute = attr
				break
	return attribute
	
def getNodesByAttribute(nodelist, name, value = ""):
	"""
	Search through all Node objects in NodeList
	"""
	if nodelist is None:
		return None

	result = []
	regex = re.compile('^' + value + '.*')
	for i in range(nodelist.length):
		node = nodelist.item (i)
		attribute = getNodeAttribute(node, name)

		found = False
		if attribute is None:
			continue
		if value is not None:
			found = True
			# Uncomment the following to see what values for 'id's
			# actually exist in the nodelist provided. This way
			# we know what to search for when looking for hardware
			# This is a little more verbose than the 'classes'
			# list above
			#print attribute.nodeValue
			if regex.match(attribute.value) == None:
				found = False
		if found:
			result.append(attribute)
	return result

def __convert(root, child=None):
	"""
	Change attributes of node to an ELEMENT node to its child nodes.
	"""

	# Only doc and element nodes can have child nodes
	isDoc = child.nodeType == child.DOCUMENT_NODE;
	isElement = child.nodeType == child.ELEMENT_NODE;

	if not isDoc and not isElement:
		return

	# Pre-conditions that need to be met
	hasChild = child.firstChild is not None
	hasChildText = child.firstChild and child.firstChild.nodeType == child.TEXT_NODE
	hasAttributes = isElement and child.hasAttributes()

	# If we are an ELEMENT node, convert our TEXT node into a
	# child node of 'value'
	if isElement and hasChild and hasChildText and hasAttributes:
		data = child.firstChild.data
		data = data.strip()
		if data:
			# Remove current TEXT node
			removed = child.removeChild(child.firstChild)
			# Create value node
			valueNode = root.createElement('value')
			valueNode.appendChild(removed)
			# Insert value node into the tree
			child.insertBefore(valueNode, child.firstChild)

	# Traverse the children
	if isDoc or isElement:
		if child.hasChildNodes():
			for c in child.childNodes:
				__convert(root, c)

	# There are no more children
	names = []

	# Create an attribute node to hold attributes
	#attrNode = root.createElement('attributes')
	if not isElement:
		return
	for i in range (child.attributes.length):
		# Get the attribute
		att = child.attributes.item(i)

		# Create an ELEMENT
		new = root.createElement(att.name)
		txt = root.createTextNode(att.name)

		# Join TEXT node with ELEMENT
		txt.nodeValue = att.value
		new.appendChild(txt)

		# For removing later
		names.append(att.name)

		# Insert into 'attribute' ELEMENT
		#attrNode.appendChild(new)

		child.appendChild(new)
	
	# Insert into the CHILD node
	#child.appendChild(attrNode)

	# Remove all attributes
	for name in names:
		child.removeAttribute(name)

def __isTraversed(name):
	global traversed

	for item in traversed:
		item == name
		return True
	return False

def __setTraversed(name):
	global traversed

	traversed.append(name)

def __collect(root, child):
	"""
	Rename any elements with the name 'node' to the 'class'
	attribute instead
	NOTE: Attributes are required for this to function
	correctly
	"""
	for n in child.childNodes:
		if n.nodeName == 'node':
			attr = getNodeAttribute(n, 'class')
			if attr is not None:
				root.renameNode(n, '', attr.value)
				#print "Changing node =>",attr.value
		
		if n.hasChildNodes():
			__collect(root,n)

def __collectBetter(root, child):
	"""
	Rename any elements with the name 'node' to the 'class'
	attribute iff the 'class' attribute has not been used
	already. Otherwise, the 'id' attribute is used'
	NOTE: Attributes are required for this to function
	correctly
	"""
	for n in child.childNodes:
		if n.nodeName == 'node':
			attrClass = getNodeAttribute(n, 'class')
			attrID    = getNodeAttribute(n, 'id')

			renameTo = ""
			if not attrClass or not attrID:
				pass

			if __isTraversed(attrClass.value):
				renameTo = attrID.value
			else:
				renameTo = attrClass.value

			__setTraversed(renameTo)

			if renameTo:
				root.renameNode(n, '', renameTo)
				#print "Changing node =>",attr.value
		
		if n.hasChildNodes():
			__collectBetter(root,n)

def __find(root, child):
	"""
	Create a list (with correct hierarchy) of devices that
	we are interested in. This gives a better view of what
	devices exist rather than looking at the output of
	'lshw -xml' as the latter is too verbose
	"""
	names = []
	for n in child.childNodes:
		if n.nodeName == 'node':
			attr = getNodeAttribute(n, 'class')
			if attr is not None:
				fullName = '+ ' + attr.value
				names.append(fullName)
			#else:
			#	names.append( 'node - no attrib' )
		else:
			pass
			#names.append ( '.' )

		if n.hasChildNodes():
			r = __find(root, n)
			if len(r) > 0:
				names.append(r)
	return names

#######################
## Configuration XML ##
#######################

def simpleDictToXml(ourDict={}, tag1='', tag2=''):
	"""
	Syntax: dictionary, first tag, second tag
	
	Function to turn any base dictionary from:
	
	{ this: that, these: those }
	
	into:
	
	<value>this</value><id>that</id><value>these</value><id>those</id>

	If passed with the value, id tags.
	"""
	# Append to this to return at end
	result = ''
	# For each item in our dictionary
	for i in ourDict:
		# Ensure we have a string
		if i.__class__ is str:
			# Make our first element, no spaces, all lower
			value = ourDict[i] # <---must generate BEFORE altering i
			i = i.replace(' ', '_')
			i = i.replace('-', '_')
			i = i.replace('.', '_')
			i = i.lower()
			result = result + '<setting><' + tag1 + '>' + i + '</' + tag1 + '>'
			# Build our second element
			if value.__class__ is str:
				value = value.strip()
				result = result + '<' + tag2 + '>' + value + '</' + tag2 + '></setting>'

			else:
				# Making recursive
				sys.stderr.write('Error: simpleDictToXml cannot handle the following non-string "Value": ')	
				sys.stderr.write(str(value))
				sys.exit(1)
				#result = result + simpleDictToXml(value, tag1, tag2)
		else:
			sys.stderr.write('Error: simpleDictToXml cannot handle the following non-string "instance": ')
			sys.stderr.write(str(i))
			sys.exit(1)
	return result

def getLsiAdpConfig(ourDict={}):
	"""
	This function is used in conjunction with simpleDictToXml()
	to create the "configuration" XML for the LSI controller.
	"""
	if ourDict == {}:
		return None
	result = '<configuration>'
	# Set to make changes easier if needed
	id = 'id'
	value = 'value'
	for i in ourDict:
		# Skip the PCI Info section
		if i == "PCI Info":
			if debug:
				echo('Found PCI Info, skipping.')
			continue
		newDict = ourDict[i]
		# If we do not get a dictionary back, keep moving
		if newDict.__class__ is not dict:
			if debug: echo('newDict is not a dict class, skipping')
			continue
		# if all looks good, parse and append to our result
		if debug:
			echo('ready to parse and merge!')
			print newDict
		result = result + simpleDictToXml(newDict, id, value)
	
	# Close it up
	result = result + '</configuration>'
	return result

def get3wareAdpConfig(ourDict={}):
	"""
	This function is used to create the "configuration" XML
	for the 3ware RAID card. Also used to build our SATA
	info in conjunction with getDiskFeatures. Should turn this
	into a class, but time doesn't allow it.
	"""
	if ourDict == {}:
		return None
	result = '<configuration>'
	# Set to make changes easier if needed
	id = 'id'
	value = 'value'
	# For each dictonary key/value, wrap 'em up into the syntax
	# that lshw uses.
	result = result + simpleDictToXml(ourDict, id, value)
	# Close it up
	result = result + '</configuration>'
	return result

def getDiskFeatures(disk=''):
	"""
	Function to add our new features as found by hdparm into 
	our SATA disk item.
	"""
	ourXML = get3wareAdpConfig(hdparmFeatures(disk))
	return ourXML


############################
## Generic RAID functions ##
############################

def findRaid():
	"""
	Finds what type(s) of RAID we have in the system.
	Supports: 3ware, LSI
	Values exported: have3wareRaid, haveLsiRaid
	Values given: True, False

	If any RAID is found, returns True 
	If no RAID is found, returns False
	"""
	# Make sure our objects are set to global
	global have3wareRaid
	global haveLsiRaid

	# Run lspci and store it
	lspciRun = runCommand('lspci')
	lspci = stringToList(lspciRun)

	# Dirty hack to see if there is a RAID bus controller.
	# This will normally appear as follows:
	#
	# 05:01.0 RAID bus controller: LSI Logic / Symbios Logic MegaRAID
	# 02:01.0 RAID bus controller: 3ware Inc 7xxx/8xxx-series PATA/SATA-RAID
	#
	# This is necessary to differentiate between systems that don't have
	# RAID but still use an LSI host controller like the following:
	#
	# 00:10.0 SCSI storage controller: LSI Logic / Symbios Logic 53c1030
	#
	hasRAID = findLines('RAID', lspci)

	if hasRAID is None:
		have3wareRaid = False
		haveLsiRaid = False
		return False
	# Audit does not yet support >1 card. Safeguard.
	elif len(hasRAID) != 1:
		sys.stderr.write('ERROR: %s RAID controllers found! Not supported.\n'
		% (len(hasRAID),))
		sys.exit(1)

	# Check if we have ANY 3ware devices
	has3ware = findLine('3ware', lspci)
	if has3ware is not None:
		if debug: echo('Found a 3ware card!\n')
		have3wareRaid = True
	else:
		if debug: echo('No 3ware cards found.\n')
		have3wareRaid = False
	
	# Check if we have ANY LSI devices
	hasLsi = findLine('LSI', lspci)
	if hasLsi is not None:
		if debug: echo('Found an LSI card!\n')
		haveLsiRaid = True
	else:
		if debug: echo('No LSI cards found.\n')
		haveLsiRaid = False
	
	# If we have either 3ware or LSI RAID present
	# return a True value. Otherwise, return false.
	if have3wareRaid or haveLsiRaid:
		return True
	else:
		return False

def findRaidInfo():
	"""
	Finds all RAID devices on the system via lspci.
	Returns a list with: PCI Address, Name, Vendor ID and Model ID
	"""
	# Get the required info from lspci and lspci -n
	lspciRun = runCommand('lspci')
	lspci = stringToList(lspciRun)
	lspciNRun = runCommand('lspci -n')
	lspciN = stringToList(lspciNRun)

	# Find our RAID card(s)
	cards = findLines('RAID', lspci)
	cardInfo = []
	# For each card found, find the lspci -n associated
	if cards is not None:
		for i in cards:

			# Get the PCI address for the card
			pci = i.split(' ')[0]
			# Pulls the name of our RAID card
			name = i.split(':')[-1].strip()
			# Find the vendor/model
			actual = findLine(pci, lspciN)
			actual = actual.split(' ')[2]
			vendor, model = actual.split(':')

			# Insert our results as a list into the main list
			result = [ pci, name, vendor, model ]
			cardInfo.append(result)

		return cardInfo
	# Return None if no cards present
	else:
		return None


def getRaidCli(make, model):
	"""
	Downloads and extracts the required tgz file and installs it 
	into the temp dir. Ideally (imo), we should be installing these 
	utils by default into the sbresuce initrd and we can stop 
	using this function.
	The tools is dowloaded from a local Kickstart server. For each
	DC where this script runs, the local Kickstart server must be
	accessible in order to retrieve this tool. The DC variable is
	read in from the ENVIRONMENT variables set by the parent
	audit.txt taskfile
	"""

	dc = None
	try:
		dc = os.environ['DC']
	except:
		sys.stderr.write("Unable to determine DC to download RAID CLI utils.\n") 
		sys.exit(1)

        server = 'kickstart.%s' % (dc,)
        path = '/kickstart/taskfiles.new/devices/%s/%s/tools.tgz' % (make, model)

        url = 'http://' + server + path
	hostname = url.split('/')[2]
	
	# Create a tmp dir if it doesn't exist
	if not os.path.isdir(tmpdir):
		os.makedirs(tmpdir, mode=0755)

	# Create a temp location to save our download to
	(fd, cliDL) = tempfile.mkstemp(prefix='tools', suffix='.tgz', dir=tmpdir)

	# Try to download our file
	try:
		if debug: echo('Attempting to download ' + url + ' to ' + cliDL + '\n')
		(filename, headers) = urllib.urlretrieve(url, cliDL)
	except:
		sys.stderr.write("Unable to connect to " + hostname + " while downloading RAID CLI utils.\n")
		sys.exit(1)

	# If no errors, go into tmpdir and extract.
	cwd = os.getcwd()
	os.chdir(tmpdir)
	runCommand('tar -xzf ' + cliDL)
	# If no errors, extract cli.tgz
	runCommand('tar -xzf cli.tgz') # This will fail if contents are renamed
	# If no errors, delete.
	os.unlink(cliDL)
	os.unlink('cli.tgz')
	# Go back to our original folder
	os.chdir(cwd)

###################################
## 3ware Specific RAID functions ##
###################################

def get3wareRaidControllers():
	"""
	Finds the controller names such as c0/c1 for 3ware
	and returns a list of controller names.
	"""
	controllers = []
	# Run the tw_cli and store into a list 
	showAll = runCommand(tw_cli + ' info')
	showAll = stringToList(showAll)

	# Find all lines that start with c0, c1. etc
	ourStrings = findLines('^c[0-9] ', showAll)

	for i in ourStrings:
		controllers.append(i.split()[0])

	return controllers


def get3wareCliInfo(controllers=[]):
	"""
	Function that takes a list of controller names and returns 
	back a dictionary listing of the raw data provided by the 
	tw_cli when running 'tw_cli info'
	"""
	
	# Store 'show all' into a list
	showAll = runCommand(tw_cli + ' info')
	showAll = stringToList(showAll)	
	
	# Find our lines with the pertinent info
	# Titles - This will only print once
	ourString = findLine('Model', showAll)
	
	# Data - This *could* show up multiple times. This
	# is the data associated with the above titles.
	ourInfo = []
	for i in controllers:
		ourStrings = findLines(i, showAll)

		# For each string in our list "ourStrings", strip out the 
		# info we need from its random # of spaces, create a list, 
		# and append it into our root list "ourInfo"
		for string in ourStrings:
			list = string.split()
			ourInfo.append(list)

	# Strip out the info from the billion spaces and create a list
	# Our titles is a single list
	ourTitles = ourString.split()

	# Ensure everything matches up
	result = []
	for i in ourInfo:
		value = mergeListsIntoDict(ourTitles, i)
		result.append(value)
	# Return our list of dictionaries
	return result


def get3wareCliDetails(controllers=[]):
	"""
	Takes a list of controllers and returns the details about
	the controller found when running 'tw_cli /c0 show all' and 
	is listed with the '/c0 ' in front.
	"""
	results = []
	# In case multiple controllers are passed
	for i in controllers:
		# Our controller Name (ex. /c0)
		ourController = '/' + i
		# Store the output of tw_cli /cN show all into a list
		details = runCommand(tw_cli + ' ' + ourController + ' show all')
		details = details.split('\n\n')[0]
		# Grab only the 1st section
		details = stringToList(details)
		# Save the lines we need
		#ourLines = findLines('^' + ourController + ' ', details)

		# Empty array to store the following loop's info into.
		cleanLines = []

		# Strip off the controller name 
		for i in details:
			cleanLines.append(i.lstrip(ourController))

		# Take our clean lines, then strip and split by = and 
		# make into list using map()
		ourDict = {}
		for i in cleanLines:
			splitValues = map(lambda i: i.strip(), i.split('='))
			# Insert the results into the dictionary
			ourDict[splitValues[0]] = splitValues[1]
			# Append our results into our final list "details"
			
		results.append(ourDict)

	return results


def get3wareArrayDetails(controllers=[]): # This should be a list in a list
	"""
	Checks if a RAID array is built on the specified controller(s). 
	If there is one or more, it builds a list of dictionaries presenting 
	the info 
	"""
	results = []

	for i in controllers:
                # Our controller Name (ex. /c0)
                ourController = '/' + i 
                # Store the output of tw_cli /cN show all into a list
                ourLines = runCommand(tw_cli + ' ' + ourController + ' show all')
		# Grab only the 2nd section
		ourLines = ourLines.split('\n\n')[1]
                ourLines = stringToList(ourLines)

		# Make sure an array is even present (could be more than one)
		arrayData = findLines('^u[0-9] ', ourLines)
		if arrayData is None:
			return results
		
		# If we are here, yey, we have RAID info! Find the titles
		arrayTitles = findLine('^Unit ', ourLines)
		# Make sure we got them
		if arrayTitles is None:
			return results

		# If we're still here, we have the required info.
		# Turn our title string into a list.
		arrayTitles = arrayTitles.split()
		arrayDataList = []
		# Turn our array string(s) into a list.
		for i in arrayData:
			arrayDataList.append(i.split())

		# For each array found, merge the data with the titles.
		for i in arrayDataList:
			results.append(mergeListsIntoDict(arrayTitles, i))

	return results


def get3warePortDetails(controllers=[]): # This REALLY needs to be a list in a list
	"""
	Finds the disks connected to the RAID controller's ports
	Returns a list of dictionaries associating all values.
	"""
	results = []

	for i in controllers:
                # Our controller Name (ex. /c0)
                ourController = '/' + i 
                # Store the output of tw_cli /cN show all into a list
                ourLines = runCommand(tw_cli + ' ' + ourController + ' show all')
		# Grab only the 3rd section
		ourLines = ourLines.split('\n\n')[2]
                ourLines = stringToList(ourLines)

		# Make sure an array is even present (could be more than one)
		portData = findLines('^p[0-9] ', ourLines)
		if portData is None:
			return results
		
		# If we are here, yey, we have RAID info! Find the titles
		portTitles = findLine('V?Port ', ourLines)
		# Make sure we got them
		if portTitles is None:
			return results

		# If we're still here, we have the required info.
		# Turn our title string into a list.
		portTitles = portTitles.split()
		# Find out which 3ware device is being used. 
		# The 8006/9650 provide 6 items, the 9690 provides 8.
		titleLength = len(portTitles)
		portDataList = []
		# 8006/9650
		if titleLength == 6:
			if debug: print ' get3warePortDetails - Found an 8006/9650 Card.'
			# Turn our array string(s) into a list.
			for i in portData:
				portDataList.append(i.split())

		# 9690
		elif titleLength == 8:
			if debug: print ' get3warePortDetails - Found a 9690 Card.'
			# Parse the data for each port using regex.
			# This regex is a beast, so going to explain it.
			regex = '(\w+)\s+(\w+)\s+(\w+)\s+(\w+.?\w+)\s+\w+\s+(\w+)\s+(\w+)\s+(.*?)\s+(.*)'
			# Actual data given: "p0    OK             u0   279.39 GB SAS   0   -            SEAGATE ST3300656SS"
			# Data we parse it into: ('p0', 'OK', 'u0', '279.39', 'SAS', '0', '-', 'SEAGATE ST3300656SS')
			# We strip out the GB (like above), and capture the entire HDD "Model"
			# Had to use regex to grab the last part and remove the GB in a sane fashion.
			for i in portData:
				# Parse the line
				ourRegexList = regexSplit(regex, i)
				# Append our list into the portDataList
				portDataList.append(ourRegexList)
			
		# Unsupported and expecting results, system exit with error message.
		else:
			sys.stderr.write("ERROR: Unsupported 3ware RAID device.\n")
			sys.stderr.write("Unable to populate the port data. Exiting.\n")
			sys.exit(1)

		# For each array found, merge the data with the titles.
		for i in portDataList:
			# Removing the GB size value from 8006/9650.
			if i[4] == 'GB':
				i.remove('GB')
			# If we have TB, then remove it and multiply 
			# it's value by 1024 per Donnie
			if i[4] == 'TB':
				i.remove('TB')
				i[3] = float(i[3])
				i[3] = i[3] * 1024
				i[3] = str(i[3])
			results.append(mergeListsIntoDict(portTitles, i))

	return results


#################################
## LSI Specific RAID functions ##
#################################

def getLsiRaidControllers():
	"""
	Finds the controller names such as Adapter #0/1/2 for LSI cards.
	and returns a list of controller names with just the number.
	"""
	controllers = []
	# Run the MegaCli and store into a list 
	showAll = runCommand(megaCli + ' -pdlist -aall')
	showAll = stringToList(showAll)

	# Find all lines that start with Adapter #0, etc.
	ourStrings = findLines('^Adapter #[0-9]+$', showAll)
	
	if ourStrings is not None:
		for i in ourStrings:
			# Find the #, since that's all LSI tracks
			regex = re.search('^Adapter #([0-9]+)$', i)
			if regex is not None:
				controllers.append(regex.group(1))
			else:
				if debug:
					return 'GLRCregexFailed'

		return controllers
	else:
		return None


def getLsiPdlistInfo(controllers=[]): # This should also be a list in a list
	"""
	Pulls all info from LSI ports using the -PDList command
	Returns a list of dictionaries, one for each port.
	"""
	ports = []
	for controller in controllers:
		# Run our command and save it
		pdlistOut = runCommand(megaCli + ' -pdlist -a' + controller)
		# Split by double paragraphs
		pdlistOut = pdlistOut.split('\n\n')

		# Doing this out of lambda since it's not cooperating.
		# Should really be using the map() + lambda on this to
		# to split and strip each element at once. 
		cleanOut = []
		for i in pdlistOut:
			cleanOut.append(i.strip())

		# Wipe out our first item if it's our controller.
		ourController = 'Adapter #' + controller
		if cleanOut[0] == ourController:
			cleanOut.remove(ourController)

		# Now to build our two lists
		list1 = []
		list2 = []

		# split into 2 lists to merge as a dict
		for i in cleanOut:
			ourLines = stringToList(i)
			for j in ourLines:
				value1, value2 = j.split(':')
				list1.append(value1)
				list2.append(value2)
			
			
			ports.append(mergeListsIntoDict(list1, list2))
	
	return ports

def getLsiAdpAllInfo(controllers=[]):
	"""
	Pulls all info about the LSI controller itself using 
	the -AdpAllInfo command. Returns a list of dictionaries.
	Each item in the list is for each controller given.
	{ Title : { Value : Data } }
	"""
	results = []
	for controller in controllers:
		# Empty lists to store the data we're parsing
		values = []
		data = []
		# Run our command and save it
		adpOut = runCommand(megaCli + ' -AdpAllInfo -a' + controller)

		# Remove the header (first four lines) which contains:
		# Adapter #0
		# ==============
		headerStrip = re.compile(r"""\s+\nAdapter #\d+\n\s*\n\=+\n""", re.MULTILINE)
		adpOut = re.sub(headerStrip, '', adpOut)

		# This grabs our titles
		titles = re.compile(r"""\s+\b(.*)\b:?$\n\s+\=+$""", re.MULTILINE)
		titleList = titles.findall(adpOut)

		# This grabs the values between the titles.
		values = re.compile(r"""\s+.*$\n\s+\=+$\n""", re.MULTILINE)
		valueList = values.split(adpOut)
		# After we split, we should get an empty value for the first item in the list
		# Instead of breaking regex, we do a simple check if [0] is empty. If it is, we
		# then remove anything that's ''.

		if valueList.count('') is not 0:
			valueList.remove('')

		# Now, let's verify the two line up.
		titleLength = len(titleList)
		valueLength = len(valueList)
		if titleLength != valueLength:
			sys.stderr.write("ERROR: getLsiAdpAllInfo failed to pull our titles/values.\n")
			sys.stderr.write("Pertinent info:\n\n")
			sys.stderr.write("Controller: " + str(controller) + '\n')
			sys.stderr.write("Length of titles: " + str(valueLength) + '\n')
			sys.stderr.write("Length of values: " + str(titleLength) + '\n')
		# Now, we need to split up our values into dictionaries
		full = {}
		for num, value in enumerate(valueList):
			list1 = []
			list2 = []
			portAddrInfo = []
			# Get our title:
			title = titleList[num]
			# Check for normal : separated lines
			isNormal = matchAllLines(r"""^(.*)\s*:\s*(.*)$""", value)
			if isNormal:
				value = stringToList(value)
				for line in value:
					value1, foo, value2 = line.partition(':')
					value1, value2 = value1.strip(), value2.strip()
					list1.append(value1)
					list2.append(value2)

				# Build our dict
				endValue = mergeListsIntoDict(list1, list2)
				full[title] = endValue
				# Don't check against anything else. 
				# Continue to our next value.
				continue
			
			# Check for our single word responses (like Pending Images In Flash = None)
			singleLine = singleLineCheck(value)
			if singleLine is True:
				full[title] = value
				# Don't check against anything else. 
				# Continue to our next value.
				continue
			# Check for stuff that wraps it's values to the next line
			wrappedValueRegex = r"""^(.*?)\s*:\n?\s?(.*)$""" 
			# Check *just* for the blah:\nmore stuff section
			wrappedValue = regexMultiMatch(r"""^(.*)\s*:\n\w+(.*)""", value)
			if wrappedValue is True:
				# Using the above regex, return a set of tuples with our info
				wrappedValues = re.findall(wrappedValueRegex, value, re.M)
				# Turn into dict
				endValue = dict(wrappedValues)
				full[title] = endValue
				continue
			# Check for the two-part object of this : that and port *8 spaces* addr
			lastCheck = checkForPortAddresses(value)
			if lastCheck is True:
				# Remove blank lines and turn into a list
				value = value.replace('\n\n','\n')
				value = stringToList(value)
				for line in value:
					# First check for normal this : that stuff
					if verifyRegex(r""".*?\s*:\n?\s?.*""", line) is not None:
						value1, foo, value2 = line.partition(':')
						value1, value2 = value1.strip(), value2.strip()
						list1.append(value1)
						list2.append(value2)
						if debug: echo('Successfully parsed normal values in Port Info\n')
					elif verifyRegex(r"""\d+\s{8,}\w+""", line) is not None:
						result = re.search(r"""(\d+)\s{8,}(\w+)""", line)
						if result is not None:
							portAddrInfo.append(result.groups())
						else:
							sys.stderr.write('ERROR: Unable to parse port info!\n')
							sys.exit(1)
					else:
						sys.stderr.write('ERROR: Unable to parse value properly.\n')
						sys.stderr.write('Trying to parse PortAddresses for LSI card.\n')
						sys.exit(1)

				# Now, build the port insanity
				# First, remove the last elements, then assign them to objects
				parent1, parent2 = list1[-1], list2[-1]
				del list1[-1]
				del list2[-1]
				# Now build our dict
				portDict = {}
				# each set of port# / Addr
				for i in portAddrInfo:
					# Get our port and address out of i
					(port, addr) = i
					# { 0 : { Address: 0152342xxxx } }
					portDict[port] = { parent2 : addr }
				# Zip everything together at the top and insert the Port: info in as well.
				endValue = mergeListsIntoDict(list1, list2)
				endValue[parent1] = portDict
				full[title] = endValue
			else:
				sys.stderr.write('ERROR: Unable to parse the following object:\n')
				sys.stderr.write('######Start#####\n' + value + '\n#####Stop#####\n\n')
				sys.exit(1)
		# Put this controller's dictionary into our list
		results.append(full)

	return results

# This needs moved up
def matchAllLines(regex='', string=''):
	"""
	Used to verify if a certain regex-compaible string 
	is found in the passed string. Turns the value string into 
	a list by lines and processes that way. Returns True/False
	"""
	# Skip entirely if either is blank.
	if regex is None or string is None:
		return None
	# Compile our stuff
	ourRegex = re.compile(regex)
	# Turn into a list
	stringList = stringToList(string)
	# Remove any empty values
	if stringList.count('') is not 0:
		stringList.remove('')
	# Find the numbers of items we have
	firstNum = len(stringList)
	# Number of values that match our regex
	secondNum = 0
	# For each item, if it matches, count+1
	for i in stringList:
		check = ourRegex.match(i)
		if check is not None:
			secondNum += 1
	# Check if our numbers match
	if debug: 
		print firstNum, secondNum
	if firstNum == secondNum:
		return True
	else:
		return False

def singleLineCheck(string=''):
	"""
	Checks if a string passed is a single line. Does 
	not verify the validity of any data, simply ensures 
	the data given is a single line and has no colons, 
	since a colon means we are supposed to split the data.
	Returns either True or False.
	"""
	if string is None:
		return None
	# Check for new lines
	newLineCheck = re.search('\n', string)
	# We want to find nothing.
	if newLineCheck is not None:
		return False
	# Now check for a colon
	splitCheck = re.search(':', string)
	if splitCheck is not None:
		return False
	# If there's one line and no colons, it's a
	# single line of "value"
	return True

def regexMultiMatch(regex='', string=''):
	"""
	Mainly used for finding specific regex matches when 
	parsing the LSI cards.
	"""
	# Make sure we have everything
	if string is None or regex is None:
		return None

	search = re.compile(regex, re.M)
	test = search.search(string)
	if test is None:
		return False
	else:
		return True

def checkForPortAddresses(string=''):
	"""
	Used to check for Port  :  Address info 
	from the AdpAllInfo output.
	Responses: True, False
	"""

	# Remove blank lines
	string = string.replace('\n\n', '\n')

	# Find the number of lines
	stringList = string.split('\n')
	numberOfLines = len(stringList)
	if debug: print numberOfLines, 'lines found.'

	# Check for normal this : that lines.
	regexCount1 = 0
	regexCount2 = 0
	for i in stringList:
		check = re.search(r"""(.*)\s*:\s*(.*)""", i)
		if check is not None:
			regexCount1 += 1
	if debug: print regexCount1, 'lines found with normal colon syntax.'

	# Now find our port lines
	for i in stringList:
		check2 = re.search(r"""\d+\s{8,}\w+""", i)
		if check2 is not None:
			regexCount2 += 1
	if debug: print regexCount2, 'lines matched with port data.'

	# Combine both lists
	regexCount3 = regexCount1 + regexCount2 
	if debug: print regexCount3, 'lines matched after searching for both regex types.'

	if numberOfLines == regexCount3:
		if debug: print 'Success: This module matches.'
		return True
	else:
		if debug: print 'Skipping.'
		return False

def getLsiDiskDescription(ourDict={}):
	"""
	Function to find if we have a SAS or SATA disk for our 
	LSI card. Creates the <description> tags.
	"""
	key = 'SAS Address(1)'
	result = '<description>'
	if ourDict.has_key(key):
		if ourDict[key] != '':
			result = result + 'SAS Disk'
		else:
			result = result + 'ATA Disk'
	result = result + '</description>'
	return result

def getLsiDiskVendor(ourDict={}):
	"""
	Function to create the <vendor> tag for the LSI card.
	"""
	result = '<vendor>'
	key = 'Inquiry Data'
	if ourDict.has_key(key):
		info = ourDict[key].split()
		if info[0] == 'ATA':
			del info[0]
		info = info[0]
	result = result + info + '</vendor>'
	return result

def getLsiDiskProduct(ourDict={}):
	"""
	Function to create the <product> tag for the LSI card.
	"""
	result = '<product>'
	key = 'Inquiry Data'
	if ourDict.has_key(key):
		info = ourDict[key].split()
		if info[0] == 'ATA':
			del info[0]
		info = info[1]
	result = result + info + '</product>'
	return result

def getLsiDiskSerial(ourDict={}):
	"""
	Function to create the <serial> tag for the LSI card.
	"""
	result = '<serial>'
	key = 'Inquiry Data'
	if ourDict.has_key(key):
		result = result + ourDict[key].split()[-1]
	result = result + '</serial>'
	return result

def getLsiDiskSize(ourDict={}):
	"""
	Function to create the <size> tag for the LSI card.
	"""
	result = '<size>'
	key = 'Raw Size'
	if ourDict.has_key(key):
		data = ourDict[key].split()[0]
		# Get both value and units
		data2 = regexSplit(r"""^(\d+)(\w+)""", data)
		if len(data2) == 2:
			# Make our value
			result = result + '<value>' + data2[0] + '</value>'
			# Make our unit
			if data2[1] == 'KB':
				result = result + '<units>kilobytes</units>'
			if data2[1] == 'MB':
				result = result + '<units>megabytes</units>'
			if data2[1] == 'GB':
				result = result + '<units>gigabytes</units>'
			if data2[1] == 'TB':
				result = result + '<units>terabytes</units>'

	result = result + '</size>'
	return result

def getLsiDiskPhysid(ourDict={}):
	"""
	Function to create the <physid> tag for the LSI card.
	"""
	result = '<physid>'
	key = 'Slot Number'
	if ourDict.has_key(key):
		result = result + ourDict[key]
	result = result + '</physid>'
	return result

def buildLsiDiskInfo(ourDict={}):
	"""
	Build our entire <disk> section for the LSI card
	"""
	result = '<disk>'
	result = result + getLsiDiskDescription(ourDict) 
	result = result + getLsiDiskProduct(ourDict)
	result = result + getLsiDiskVendor(ourDict)
	result = result + getLsiDiskPhysid(ourDict)
	result = result + getLsiDiskSerial(ourDict)
	result = result + getLsiDiskSize(ourDict)
	result = result + '<class>disk</class></disk>'
	return result



def get3wareDiskDescription(ourDict={}):
	"""
	Function to create the <description> tag for the 3ware card.
	"""
	key = 'Type'
	result = '<description>'
	if ourDict.has_key(key):
		if ourDict[key] == 'SAS':
			result = result + 'SAS Disk'
		else:
			result = result + 'ATA Disk'
	else:
		result = result + 'ATA Disk'
	result = result + '</description>'
	return result	

def get3wareDiskProduct(ourDict={}):
	"""
	Function to create the <product> tag for the 3ware card.
	"""
	key = 'Model'
	result = '<product>'
	if ourDict.has_key(key):
		result = result + ourDict[key].split()[-1]
	result = result + '</product>'
	return result

def get3wareDiskVendor(ourDict={}):
	"""
	Function to create the <vendor> tag for the 3ware card.
	"""
	key = 'Model'
	result = '<vendor>'
	if ourDict.has_key(key):
		result = result + ourDict[key].split()[0]
	result = result + '</vendor>'
	return result

def get3wareDiskPhysid(ourDict={}):
	"""
	Function to create the <physid> tag for the 3ware card.
	"""
	key1 = 'Port'
	key2 = 'VPort'
	result = '<physid>'
	if ourDict.has_key(key1):
		# Strip out the p
		pStrip = regexSplit(r"""\w(\d+)""", ourDict[key1])[0]
		# Strip out the p
		result = result + pStrip
	elif ourDict.has_key(key2):
		# Strip out the p
		pStrip = regexSplit(r"""\w(\d+)""", ourDict[key2])[0]
		result = result + pStrip
	result = result + '</physid>'
	return result

def get3wareDiskSerial(ourDict={}):
	"""
	Function to create the <serial> tag for the 3ware card.
	"""
	key = 'Serial'
	result = '<serial>'
	if ourDict.has_key(key):
		result = result + ourDict[key]
	result = result + '</serial>'
	return result

def get3wareDiskSize(ourDict={}):
	"""
	Function to create the <size> tag for the 3ware card.
	"""
	key = 'Size'
	result = '<size>'
	if ourDict.has_key(key):
		result = result + '<value>' + ourDict[key] + '</value>'
		result = result + '<units>gigabytes</units>' # cli only gives GB
	result = result + '</size>'
	return result

def build3wareDiskInfo(ourDict={}):
	"""
	Build our entire <disk> section for the 3ware card
	"""
	result = '<disk>'
	result = result + get3wareDiskDescription(ourDict) 
	result = result + get3wareDiskProduct(ourDict)
	result = result + get3wareDiskVendor(ourDict)
	result = result + get3wareDiskPhysid(ourDict)
	result = result + get3wareDiskSerial(ourDict)
	result = result + get3wareDiskSize(ourDict)
	result = result + '<class>disk</class></disk>'
	return result

##################
###### MAIN ######
##################

# This is currently used to test and confirm all functions are working
# Please do not delete anything below here for testing! Leave this and 
# run with your own test. =) This verifies everything as we go.

# Allows this script to be used as a library
if __name__ == "__main__":

	# Reminder if we're in debug mode
	if debug:
		echo('\n!!RUNNING IN DEBUG MODE!!\n\n')

	# Ensure we are root
	# Write to stderr to avoid stdout redirect from end result
	checkForRoot()
	sys.stderr.write("Verified running as root.\n")

	# Get our info from lshw and store it as dom
	dom = lshw()

	# Change nodes with the name 'node' to the value their
	# attribute 'class' is set to
	__collect(dom, dom)

	# Change attributes to child nodes of any ELEMENT node
	# that has attributes
	__convert(dom, dom)

	# Check for RAID
	weHaveRaid = findRaid()
	#weHaveRaid = False
	
	# Append stuff to our XML if we have RAID
	if weHaveRaid:
		# We need to be able to reach a server to acquire RAID tools

		raidInfo = findRaidInfo()
		cardNum = 0
		for i in raidInfo:
			pci, name, vendor, model = i[0], i[1], i[2], i[3]
			if debug:
				echo('Found ' + str(len(raidInfo)) + ' RAID Device(s)\n')
				cardNum += 1
				echo("Card #" + str(cardNum) + "\n")
				echo("Our PCI Addr:   " + pci + "\n")
				echo("Our Card Name:  " + name + "\n")
				echo("Our vendor ID:  " + vendor + "\n")
				echo("Our model ID:   " + model + "\n\n")

		# Get the RAID CLI for the respective card.
		getRaidCli(vendor, model)
		if have3wareRaid:
			controllers = get3wareRaidControllers()
			if controllers is not None:
				# Get all available info
				cardDetails = get3wareCliDetails(controllers)
				portDetails = get3warePortDetails(controllers)
				
				# Build the XML for our controller's settings
				cardList = []
				for i in cardDetails:
					cardList.append(get3wareAdpConfig(i))
				# Now, build the <disk> info
				portList = []
				for i in portDetails:
					portList.append(build3wareDiskInfo(i))
				
			
			else:
				sys.stderr.write('No 3ware controllers found but one is present.\n')
				sys.stderr.write('Exiting.\n')
				sys.exit(1)

		# This is an elif since we should only have 1 RAID card presently.
		elif haveLsiRaid:
			controllers = getLsiRaidControllers()
			if controllers is not None:
				cardDetails = getLsiAdpAllInfo(controllers)
				portDetails = getLsiPdlistInfo(controllers)
				
				# Build the XML for our controller's settings
				cardList = []
				for i in cardDetails:
					cardList.append(getLsiAdpConfig(i))
				
				# Now, build the <disk> info
				portList = []
				for i in portDetails:
					portList.append(buildLsiDiskInfo(i))
			else:
				sys.stderr.write('No LSI controllers found but one is present.\n')
				sys.stderr.write('Exiting.\n')
				sys.exit(1)
				
		else:
			sys.stderr.write('Error: Found RAID but found no cards. This is a bug\n')
			sys.exit()

		# Find our XML elements
		storage = dom.getElementsByTagName('storage')
		storage = storage[0]
		storageConfig = storage.getElementsByTagName('configuration')
		storageConfig = storageConfig[0]

		# Now append each to the dom
		for i in cardList:
			newConfig = xml.dom.minidom.parseString(i)
			data = newConfig.firstChild.childNodes
			# Do not use 'for node in data:' here. The 
			# iterations cause breakage.
			while len(data) > 0:
				storageConfig.appendChild(data[0])
		for i in portList:
			disk = xml.dom.minidom.parseString(i)
			storage.appendChild(disk.firstChild)

	# If we are here, we should be using native SATA disks
	# Time to add in our hdparm values for SATAI/SATAII stuff
	else:
		if debug:
			echo("No RAID cards present in this system. Working on SATA hdparm values.\n")
		xmlDisks = dom.getElementsByTagName("disk")
		devDisks = findDisks()

		for disk in xmlDisks:
			# First, figure out which disk we're in and build the 
			# configuration DOM for it.
			logicalName = disk.getElementsByTagName("logicalname")

			# Added this check due to failure of Dell 2950 which
			# crashes because of the following error:
			#
			# IndexError: list index out of range

			if len(logicalName) is 0:
				continue

			logicalName = logicalName[0]
			ourDisk = logicalName.firstChild.nodeValue
			# Make sure it's a valid disk
			if ourDisk not in devDisks:
				continue 
			# Build our <configuration> if it's a valid disk.
			# This should only fail if it's a RAID disk.
			diskConfig = getDiskFeatures(ourDisk)
			if diskConfig is None:
				continue

			diskDom = xml.dom.minidom.parseString(diskConfig)
			diskData = diskDom.firstChild.childNodes

			diskConfig = disk.getElementsByTagName('configuration')
			diskConfig = diskConfig[0]

			# Do not use 'for node in diskData:' here. The 
			# iterations cause breakage.
			while len(diskData) > 0:
				diskConfig.appendChild(diskData[0])

	# All done, convert back to XML and return
	output = dom.toxml('utf-8')
	# Now strip the spaces/indents/newlines
	output = stringToList(output)
	sys.stdout.write("".join(output))
	sys.exit(0)
