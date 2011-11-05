#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys, subprocess, os, socket, time, re, tempfile, urllib

print
print "$Revision: 17362 $"
print "$Date: 2007-05-24 18:31:17 -0500 (Thu, 24 May 2007) $"
print "$HeadURL: https://source.core.rackspace.com/svnroot/ubuntu/base_config/base_setup.py $"
print

def call(*args, **kw):
    exit_code = subprocess.call(*args, **kw)
    if exit_code != 0:
        raise ValueError, "Program error (exit code=%s): %s" \
            % (exit_code, args)

def debconfSetSelections(selections):
    p = subprocess.Popen(
        'debconf-set-selections', 
        stdin = subprocess.PIPE,
        )
    p.stdin.write(selections)
    p.stdin.close()

def getDeviceIPList():
    p = subprocess.Popen(
        '/sbin/ifconfig', 
        stdout = subprocess.PIPE,
        )
    ip_list = []
    device = None
    for line in p.stdout.readlines():
        if line[0] not in (' ', '\n'):
            device = line.split()[0]
            continue
        tokens = line.split()
        if tokens and tokens[0] == 'inet':
            ip = tokens[1].split(':')[1]
            ip_list.append((device, ip))
    return ip_list

def askYesNo(question):
    while 1:
        answer = raw_input("%s " % question)
        answer = answer.strip().lower()
        if answer == 'y':
            return True
        elif answer == 'n':
            return False

def backupFile(file_name):
    if os.path.exists(file_name):
        new = file_name + '.' + time.strftime('%Y-%m-%d_%H_%M_%S')
        call( ['cp', '-a', file_name, new] )

def substitute(regex_list, file_name):
    if not os.path.isfile(file_name):
        return
    file_lines = open(file_name).readlines()
    for i in range(0, len(file_lines)):
        for regex, replacement in regex_list:
            file_lines[i] = re.sub(regex, replacement, file_lines[i])
    open(file_name, 'w').writelines(file_lines)

def addLinesIfNecessary(new_lines, file_name):
    if os.path.isfile(file_name):
        file_lines = open(file_name).readlines()
    else:
        file_lines = []
    for new_line in new_lines:
        i = 0
        is_found = 0
        while i < len(file_lines):
            if new_line in file_lines[i]:
                file_lines[i] = new_line + '\n'
                is_found = 1
                break
            i += 1
        if not is_found:
            file_lines.append(new_line + '\n')
    open(file_name, 'w').writelines(file_lines)

### Configuration functions

def initialChecks():
    locations = [
        'http://apt.core.rackspace.com/base_setup.py',
        'http://source.core.rackspace.com/apt/base_setup.py',
        ]

    download = None
    for url in locations:
        try:
            print "Trying:", url
            download = urllib.URLopener().open(url)
            break
        except IOError, info:
            print
            print info
            print

    if download:
        fd, temp_name = tempfile.mkstemp(dir = '/tmp')
        fp = os.fdopen(fd, 'w')
        fp.writelines(download.readlines())
        fp.close()
    else:
        call(['install', '-m', '755', sys.argv[0], 
              '/usr/sbin/base_setup.py'])
        return

    p = subprocess.Popen(['diff', '-u', sys.argv[0], temp_name],
                          stdout = subprocess.PIPE
                        )
    diff_lines = p.stdout.readlines()

    if not diff_lines:
        if os.path.realpath(sys.argv[0]) != '/usr/sbin/base_setup.py':
            call(['install', '-m', '755', sys.argv[0], 
                  '/usr/sbin/base_setup.py'])
    else:
        while 1:
            print
            print "This base_setup.py script does not match the script from"
            print url
            print
            choices = [
                'View differences',
                'Copy downloaded file to /usr/sbin/base_setup.py and quit',
                'Continue with this script (copy it to /usr/sbin, also)'
                ]
            answer = selectFromList('Choose:', choices)
            if answer == choices[0]:
                os.system("diff -u '%s' '%s' | less"
                          % (sys.argv[0], temp_name))
            elif answer == choices[1]:
                call(['install', '-m', '755', temp_name, 
                      '/usr/sbin/base_setup.py'])
                sys.exit()
            elif answer == choices[2]:
                if os.path.realpath(sys.argv[0]) != '/usr/sbin/base_setup.py':
                    call(['install', '-m', '755', sys.argv[0], 
                          '/usr/sbin/base_setup.py'])
                break
            else:
                print "What????"



            

def configureHostName():
    file_host_name = open('/etc/hostname').read().strip()
    choose_hostname = False
    if file_host_name != socket.gethostname():
        print
        print "/etc/hostname and /bin/hostname do not match:"
        print "    /etc/hostname:", file_host_name
        print "    /bin/hostname:", socket.gethostname()
        choose_hostname = True
    else:
        print
        print '/etc/hostname contains "%s"' % file_host_name
        choose_hostname = askYesNo("Do you want to edit your hostname (y/n)?")

    if choose_hostname:
        while 1:
            host_name = raw_input("Enter hostname: ")

            print
            print '  Hostname: "%s"' % host_name
            if askYesNo("Is this hostname ok (y/n)?"):
                open('/etc/hostname', 'w').write("%s\n" % host_name)
                call(['/bin/hostname', host_name])
                break
    else:
        host_name = file_host_name

    etc_hosts = open('/etc/hosts').readlines()
    is_found = False
    for line in etc_hosts:
        for token in line.split():
            if '#' in token:
                break
            if token == host_name:
                is_found = True
                break
        if is_found:
            break

    if not is_found:
        print
        print 'Hostname "%s" was not in /etc/hosts' % host_name
        print 'I will add an entry now, since sudo will break otherwise.'
        print 'More Info: https://bugs.launchpad.net/ubuntu/+source/sudo/+bug/32906'

        try:
            ip = socket.gethostbyname(host_name)
            print "Found %s -> %s in dns" % (host_name, ip)
        except socket.gaierror:
            ip_list = getDeviceIPList()
            
            if not ip_list:
                ip = '127.0.0.1'
            else:
                while 1:
                    print
                    for i in range(0, len(ip_list)):
                        device, ip = ip_list[i]
                        print "%d: %s = %s" % (i, device, ip)
                    print
                    answer = raw_input('Which IP do you want to use for %s %s? '
                                % (host_name, tuple(range(0, len(ip_list)))))
                    try:
                        answer = int(answer)
                    except ValueError:
                        pass
                    if answer in range(0, len(ip_list)):
                        ip = ip_list[answer][1]
                        break

        new_entry = '%s    %s\n' % (ip, host_name)
        print 
        print "Adding this entry to /etc/hosts:"
        print new_entry
        etc_hosts.insert(1, new_entry)
        open('/etc/hosts', 'w').writelines(etc_hosts)
        print
        print "Restarting nscd"
        if os.path.exists('/etc/init.d/nscd'):
            call(['/etc/init.d/nscd', 'restart'])
    return host_name


def configureAptSources():
    backupFile('/etc/apt/sources.list')

    new_sources = [
        'deb http://apt.core.rackspace.com dapper main',
        'deb-src http://apt.core.rackspace.com dapper main',
        'deb http://source.core.rackspace.com/apt dapper main',
        'deb-src http://source.core.rackspace.com/apt dapper main',
        'deb http://archive.ubuntu.com/ubuntu dapper universe multiverse',
        'deb-src http://archive.ubuntu.com/ubuntu dapper universe multiverse',
        'deb http://archive.ubuntu.com/ubuntu dapper-security universe multiverse',
        'deb-src http://archive.ubuntu.com/ubuntu dapper-security universe multiverse',
        'deb http://archive.ubuntu.com/ubuntu dapper-updates universe multiverse',
        'deb-src http://archive.ubuntu.com/ubuntu dapper-updates universe multiverse',
        ]

    addLinesIfNecessary(new_sources, '/etc/apt/sources.list')


def configureAptSourcesAndProxy():
    configureAptSources()
    configureProxy()
    try:
        call(['apt-get', 'update'])
    except ValueError, info:
        print
        print 'Error during "apt-get update"'
        print

def configureEtcProfile():
    backupFile('/etc/profile')

    etc_profile_lines = [
        'export PAGER=less',
        'export EDITOR=vim',
        ]

    addLinesIfNecessary(etc_profile_lines, '/etc/profile')

def configureProxy():
    proxy_list = [
        'apt.core.rackspace.com',
        'proxy.sat.rackspace.com',
        'proxy.lon.rackspace.com',
        'proxy.dfw1.rackspace.com',
        'proxy.iad1.rackspace.com',
        'proxy.hkg1.rackspace.com',
        ]
    proxy_host = selectFromList('Choose proxy (for /root/.profile):', 
                                proxy_list)
    proxy_lines = [
        'export PROXY=%s' % proxy_host,
        'export PROXYPORT=3128',
        'export http_proxy=http://$PROXY:$PROXYPORT',
        'export https_proxy=https://$PROXY:$PROXYPORT',
        'export HTTP_PROXY=$http_proxy',
        'export HTTPS_PROXY=$https_proxy',
        ]

    addLinesIfNecessary(proxy_lines, '/root/.profile')
    os.environ['http_proxy'] = "http://" + proxy_host + ":3128"

def configureVimrc():
    vimrc_lines = [
        'set expandtab       " indent using spaces instead of tab character',
        'set softtabstop=4   " tab-key equals four spaces',
        'set shiftwidth=4    " >> and << will change indentation by 4 spaces',
        'set smartindent     " automatically indent code constructs',
        'set backspace=indent,eol,start  " vim backspace will not be stopped as in vi',
        'set showcmd         " Show (partial) command in status line.',
        'set showmatch       " Show matching brackets.',
        'set ignorecase      " Do case insensitive matching',
        'set incsearch       " Incremental search',
        'syntax on           " Show syntax highlighting',
        'au BufEnter *.py set list listchars=tab:»· " display tab chars in .py files',
        'au BufRead *.pt set syntax=php " syntax highlighting which handles <? ?>',
        'au BufRead *.phlib set syntax=php',
        ]
    addLinesIfNecessary(vimrc_lines, '/etc/vim/vimrc.local')


def configureLdap():
    # Specifying values with debconf-set-selections prevents prompting
    # for values we know, without turning off prompting entirely.
    debconf_selections = '''
# password for database login account
libnss-ldap     libnss-ldap/bindpw      password        
# Root login password
libpam-ldap     libpam-ldap/rootbindpw  password        
# Password for the login account.
libpam-ldap     libpam-ldap/bindpw      password        
# database requires login
libnss-ldap     libnss-ldap/dblogin     boolean false
# enable automatic configuration updates by debconf
libnss-ldap     libnss-ldap/override    boolean true
# The distinguished name of the search base.
libnss-ldap     shared/ldapns/base-dn   string  o=rackspace
libpam-ldap     shared/ldapns/base-dn   string  o=rackspace
# Make debconf change your config?
libpam-ldap     libpam-ldap/override    boolean true
# LDAP version to use.
# Choices: 3, 2
libnss-ldap     shared/ldapns/ldap_version      select  3
libpam-ldap     shared/ldapns/ldap_version      select  3
# Database requires logging in.
libpam-ldap     libpam-ldap/dblogin     boolean false
# unprivileged database user
libnss-ldap     libnss-ldap/binddn      string  cn=proxyuser,dc=example,dc=net
# LDAP Server host.
libnss-ldap     shared/ldapns/ldap-server       string  ldap.rackspace.com
libpam-ldap     shared/ldapns/ldap-server       string  ldap.rackspace.com
# Local crypt to use when changing passwords.
# Choices: clear, crypt, nds, ad, exop, md5
libpam-ldap     libpam-ldap/pam_password        select  crypt
# Unprivileged database user.
libpam-ldap     libpam-ldap/binddn      string  cn=proxyuser,dc=example,dc=net
# make configuration readable/writeable by owner only
libnss-ldap     libnss-ldap/confperm    boolean false
# Root login account
libpam-ldap     libpam-ldap/rootbinddn  string  cn=manager,dc=example,dc=net
# Make local root Database admin.
libpam-ldap     libpam-ldap/dbrootlogin boolean true
'''

    debconfSetSelections(debconf_selections)
    print
    print "Installing sso-coreteam package"
    print
    call(['apt-get', 'install', '--force-yes', '--yes', 'sso-coreteam'])
# end setLdap()

def configureTimeZone():
    backupFile('/etc/timezone')
    open('/etc/timezone', 'w').write('US/Central\n')
    backupFile('/etc/localtime')
    if os.path.exists('/etc/localtime'):
        os.remove('/etc/localtime')
    os.symlink('/usr/share/zoneinfo/US/Central', '/etc/localtime')
    print
    print "Configured /etc/timezone and /etc/localtime for US/Central."

def configureSyslog():
    subs = [
        (r'#cron\.\*', r'cron.*'),
        ]
    substitute(subs, '/etc/syslog.conf')
    call(['/etc/init.d/sysklogd', 'restart'])
    print 
    print "Configured /etc/syslog.conf"

def selectFromList(question, options):
    while 1:
        print
        for i in range(0, len(options)):
            print "%d: %s" % (i, options[i])
        print
        answer = raw_input('%s %s ' 
                    % (question, 
                       tuple(range(0, len(options)))
                      )
                    )
        try:
            answer = int(answer)
        except ValueError:
            continue
        if answer in range(0, len(options)):
            return options[answer]

def configureMail():
    mx_list = [
        'mx.sat.corp.rackspace.com',
        'mx.sat.rackspace.com',
        'mx.iad1.rackspace.com',
        'mx.dfw1.rackspace.com',
        'mx.lon.rackspace.com',
        'mx1.hkg1.rackspace.com',
        ]
    mx_server = selectFromList(
        'Which Mail Server do you want to proxy through?',
        mx_list)

    call(['apt-get', 'install', '--yes', 'postfix'])

    backupFile('/etc/postfix/main.cf')
    main_cf_lines = [
        (r'^myorigin *=.*', r'myorigin = /etc/hostname'),
        (r'^mydestination *=.*', r'mydestination = %s' % host_name),
        (r'^myhostname *=.*', r'myhostname = %s' % host_name),
        (r'^relayhost *=.*', r'relayhost = %s' % mx_server),
        ]
    substitute(main_cf_lines, '/etc/postfix/main.cf')

    ## /etc/aliases
    backupFile('/etc/aliases')
    subs = [
        (r'^\*:.*', r''),
        (r'^root:.*', r''),
        ]
    substitute(subs, '/etc/aliases')
    etc_aliases_lines = [
        '*: root',
        'root: core_error@rackspace.com',
        ]
    addLinesIfNecessary(etc_aliases_lines, '/etc/aliases')
    call('/usr/bin/newaliases')
    
def configureSystat():
    debconf_selections = '''
# Do you want to activate sysstat's cron job?
sysstat sysstat/enable          boolean true
sysstat sysstat/remove_files    boolean true
'''
    debconfSetSelections(debconf_selections)
    call(['apt-get', 'install', '--yes', 'sysstat'])
    backupFile('/etc/default/sysstat')
    subs = [
        (r'^ENABLED=.*', r'ENABLED="true"'),
        ]
    substitute(subs, '/etc/default/sysstat')
    call(['/etc/init.d/sysstat', 'start'])

def configureNtp():
    call(['apt-get', 'install', '--yes', 'ntp', 'ntp-server'])
    try:
        call(['/etc/init.d/ntp-server', 'stop'])
    except ValueError:
        print "ntp-server not running"

    backupFile('/etc/ntp.conf')

    subs = [
        (r'^server ', r'#\0'),
        ]
    substitute(subs, '/etc/ntp.conf')

    etc_ntp_conf_lines = ['server time.rackspace.com prefer']
    addLinesIfNecessary(etc_ntp_conf_lines, '/etc/ntp.conf')

    call(['ntpdate', 'time.rackspace.com'])
    call(['hwclock', '--systohc'])
    call(['/etc/init.d/ntp-server', 'start'])

def configureSubversion():
    call(['apt-get', 'install', '--yes', 'subversion'])
    svn_config_changes = [
        (r'#* *store-passwords.*', r'store-passwords = no'),
        ]
    substitute(svn_config_changes, '/etc/subversion/config')

def disableLocalAccounts():
    user_list = []
    for line in open('/etc/shadow'):
        tup = line.split(':')
        user, password = tup[0], tup[1]
        if user != 'root' and len(password) > 1 and password[0] != '!':
            # "usermod -L" disables passwords with "!" 
            if user not in user_list:
                user_list.append(user)
    for user in user_list:
        try:
            if askYesNo('Do you want to disable the local user "%s"?' % user):
                # usermod is better since
                # chsh asks for a password or silently fails
                call(['usermod', '-L', user])
        except ValueError, info:
            print 'Failed to disable "%s" account: %s' % (user, info)

def configureSsh():
    sshd_config_changes = [
        (r'#* *PermitRootLogin.*', r'PermitRootLogin no'),
        ]
    substitute(sshd_config_changes, '/etc/ssh/sshd_config')

def configureSnmpd():
    call(['apt-get', 'install', '--yes', 'snmpd'])
    backupFile('/etc/snmp/snmpd.conf')
    open('/etc/snmp/snmpd.conf', 'w').write('''
## sec.name source community
com2sec systems 10.1.25.5 coresnmp

# group.name sec.model sec.name
group rackspace v1 systems

# incl/excl subtree mask
view systemview included .1 80

# context sec.model sec.level prefix read write notif
access rackspace "" any noauth exact systemview none none
''')
    call(['/etc/init.d/snmpd', 'restart'])

def configureGrub():
    print
    print "We want to configure grub (/boot/grub/menu.lst) so that the bnx2"
    print "driver on HP DL385 G2 systems finds the second NIC."
    if askYesNo("Proceed with grub configuration?"):
        backupFile('/boot/grub/menu.lst')
        subs = [
            (r' *pci=nommconf', r''),
            (r'^(# *kopt=.*)', r'\1 pci=nommconf'),
            ]
        substitute(subs, '/boot/grub/menu.lst')
        call(['/sbin/update-grub'])

def installUtilities():
    print
    print "Installing extra packages"
    call([
        'apt-get', 'install', '--yes', 
        'binutils', 'grep-dctrl', 'sysv-rc-conf', 'emacs-nox',
        'logwatch', 'lynx', 'links', 'mutt', 'telnet-ssl',
        'iputils-tracepath', 'traceroute', 'sash',
        'build-essential', 'linux-headers-server',
        'postgresql-client-8.1',
        ])

    while 1:
        print
        print "### Option to Upgrade Packages ###"
        dist_upgrade_choices = [
            'View dry-run of packages to be upgraded.',
            'Run "apt-get dist-upgrade --yes"',
            'Skip dist-upgrade',
            ]
        answer = selectFromList('apt-get dist-upgrade choices:', 
                                dist_upgrade_choices)

        if answer == dist_upgrade_choices[0]:
            print "  ## dry-run ##"
            call(['apt-get', '--dry-run', 'dist-upgrade'])
        elif answer == dist_upgrade_choices[1]:
            print "  ## running dist-upgrade ##"
            call(['apt-get', 'dist-upgrade', '--yes', '--force-yes'])
            break
        elif answer == dist_upgrade_choices[2]:
            print "  ## skipping ##"
            break
        else:
            print "Whatchoo talkin' about, Willis?"

    # if "apt-get dist-upgrade" upgrades the kernel, we need
    # a newer linux-headers package
    p = subprocess.Popen(['uname', '-r'],
                         stdout = subprocess.PIPE)
    current_kernel_version = p.stdout.read().strip()
    reboot_kernel_version = None
    for line in open('/boot/grub/menu.lst'):
        m = re.match(r'^kernel.*vmlinuz-([^ ]*) .*', line)
        if m:
            reboot_kernel_version = m.group(1)
            break
    if reboot_kernel_version != current_kernel_version:
        print "Current kernel:", current_kernel_version
        print "Kernel after reboot:", reboot_kernel_version
        kernel_version = reboot_kernel_version
        is_kernel_upgraded = 1
    else:
        kernel_version = current_kernel_version
        is_kernel_upgraded = 0

    return is_kernel_upgraded


if __name__ == '__main__':
    # Only change the frontend to noninteractive as a last resort.
    #os.environ['DEBIAN_FRONTEND'] = 'noninteractive' # eliminate questions
    os.environ['DEBIAN_PRIORITY'] = 'critical' # reduce questions

    initialChecks()
    host_name = configureHostName()
    configureAptSourcesAndProxy() 

    configureEtcProfile()
    configureTimeZone()
    configureSyslog()
    configureVimrc()
    configureLdap()
    configureNtp()
    configureMail()
    configureSystat()
    configureSubversion()
    configureSsh()
    configureSnmpd()
    is_kernel_upgraded = installUtilities()
    disableLocalAccounts()
    configureGrub()
    # this should run last
    print
    print "*** Attention ***"
    print "* For some reason, networking needs to be restarted *"
    print "* before you reboot a system, so that networking    *"
    print "* will come up after the reboot.                    *"
    if askYesNo("Restart networking to reconfigure it?"):
        call(['/etc/init.d/networking', 'restart'])

    if is_kernel_upgraded:
        print
        if askYesNo('Kernel was upgraded. Do you want to reboot now?'):
            if askYesNo('Are you sure?'):
                call(['/sbin/shutdown', '-rf', '+1'])
