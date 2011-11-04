#!/usr/bin/python
# -*- coding: utf8 -*-
import os, ldap, MySQLdb 

ignore = ('all', 'stats', 'controller', 'commerce', 'abonent', 'check_engine', 'technics', 'ao', 'info', 'resume')
bind_user = "CN=jabberd2,DC=example,DC=org"
bind_password = "thepassword"
base = "OU=Office,DC=example,DC=org"

l = ldap.initialize("ldap://AD.AD.AD.AD:389")
l.simple_bind_s(bind_user, bind_password)

persons = {}
for dn, attr in l.search_s(base, ldap.SCOPE_SUBTREE, filterstr='(objectClass=person)', attrlist=('userPrincipalName', 'displayName')):
    if attr.has_key('userPrincipalName'):
        persons[dn] = (attr['userPrincipalName'][0], attr['displayName'][0])

groups = []
for dn,attr in l.search_s(base, ldap.SCOPE_SUBTREE, filterstr='(objectClass=group)', attrlist=('cn', 'member', 'description')):
    members = []
    if attr.has_key('description'):
        for member in attr['member']:
            if (persons.has_key(member)) and (persons[member][0].strip() != ''):
                members.append(persons[member])
        if (len(members) > 0) and (attr['cn'][0].lower() not in ignore):
            groups.append( (attr['cn'][0], attr['description'][0], members) )

con = MySQLdb.connect(host='localhost',user='jabberd2',passwd='thepassword',db='jabberd2')
c = con.cursor()
c.execute('SET NAMES UTF8')
groups.sort(cmp=lambda x,y: cmp(x[1].lower(), y[1].lower()))

rg = {}
c.execute('SELECT `collection-owner`, `jid`, `group` FROM  `roster-groups`')
for row in c:
    if not rg.has_key(row[0]):
        rg[row[0]] = {row[1]: [row[2]]}
    else:
        if not rg[row[0]].has_key(row[1]):
            rg[row[0]][row[1]] = [row[2]]
        else:
            rg[row[0]][row[1]].append(row[2])
ri = {}
c.execute('SELECT `collection-owner`, `jid` FROM `roster-items`')
for row in c:
    if not ri.has_key(row[0]):
        ri[row[0]] = {row[1]: 1}
    else:
        ri[row[0]][row[1]] = 1

cg, ci = 0, 0
for p in persons.itervalues():
    for g in groups:
        for member in g[2]:
            if p[0] != member[0]:
                if not (rg.has_key(p[0]) and rg[p[0]].has_key(member[0]) and (g[1] in rg[p[0]][member[0]])):
                    c.execute('INSERT INTO `roster-groups` (`collection-owner`, `jid`, `group`) VALUES (%s, %s, %s)', (p[0], member[0], g[1]))
                    cg += 1
                if not (ri.has_key(p[0]) and ri[p[0]].has_key(member[0])):
                    c.execute('INSERT INTO `roster-items` (`collection-owner`, `jid`, `name`, `to`, `from`, `ask`) VALUES (%s, %s, %s, 1, 1, 0)', (p[0], member[0], member[1]))
                    if not ri.has_key(p[0]):
                        ri[p[0]] = {member[0]: 1}
                    else:
                        ri[p[0]][member[0]] = 1
                    ci += 1
con.close()
print 'added groups=%d items=%d'%(cg, ci)