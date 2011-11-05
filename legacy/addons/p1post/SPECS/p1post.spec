Summary:	Peer1 Post Installation Routines
Name:		p1post
Version:	1.0
Release:	3
License:	GPL
ExclusiveOS: Linux
Group:		System Environment/Base
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-build
BuildArch:	noarch
Source0:	%{name}-%version.tar.gz

%description
The p1post utility facilitiates the execution of any tasks following the installation of the operating system.

%prep
%setup

%install
install -m 0755 -d $RPM_BUILD_ROOT/usr/local/p1post
install -m 0755 -d $RPM_BUILD_ROOT/usr/local/p1post/lib
install -m 0755 -d $RPM_BUILD_ROOT/usr/local/p1post/script.d
install -m 0755 -d $RPM_BUILD_ROOT/usr/local/p1post/logs
install -m 0755 -d $RPM_BUILD_ROOT/usr/local/p1post/state
install -m 0755 -d $RPM_BUILD_ROOT/usr/local/p1post/tmp
install -m 0755 -d $RPM_BUILD_ROOT/usr/local/p1post/keys


install -m 0755 -d $RPM_BUILD_ROOT/etc
install -m 0755 -d $RPM_BUILD_ROOT/etc/init.d
install -m 0755 -d $RPM_BUILD_ROOT/etc/yum.repos.d
install -m 0755 -d $RPM_BUILD_ROOT/etc/sysconfig

install -m 0755 yum.repos.d/kickstart.repo $RPM_BUILD_ROOT/etc/yum.repos.d/kickstart.repo

install -m 0644 usr/local/p1post/keys/55BE302B.gpg $RPM_BUILD_ROOT/usr/local/p1post/keys/55BE302B.gpg
install -m 0644 usr/local/p1post/keys/F42584E6.gpg $RPM_BUILD_ROOT/usr/local/p1post/keys/F42584E6.gpg

install -m 0755 usr/local/p1post/lib/config.sh $RPM_BUILD_ROOT/usr/local/p1post/lib/config.sh
install -m 0644 usr/local/p1post/lib/keep_me $RPM_BUILD_ROOT/usr/local/p1post/lib/keep_me
install -m 0755 usr/local/p1post/lib/p1ks_lib.sh $RPM_BUILD_ROOT/usr/local/p1post/lib/p1ks_lib.sh

install -m 0644 usr/local/p1post/logs/keep_me $RPM_BUILD_ROOT/usr/local/p1post/logs/keep_me

install -m 0755 usr/local/p1post/script.d/00fixNamed.sh $RPM_BUILD_ROOT/usr/local/p1post/script.d/00fixNamed.sh
install -m 0755 usr/local/p1post/script.d/00p1admin.sh $RPM_BUILD_ROOT/usr/local/p1post/script.d/00p1admin.sh
install -m 0755 usr/local/p1post/script.d/10fixphpini.sh $RPM_BUILD_ROOT/usr/local/p1post/script.d/10fixphpini.sh
install -m 0755 usr/local/p1post/script.d/15raid.sh $RPM_BUILD_ROOT/usr/local/p1post/script.d/15raid.sh
install -m 0755 usr/local/p1post/script.d/20rhn.sh $RPM_BUILD_ROOT/usr/local/p1post/script.d/20rhn.sh
install -m 0755 usr/local/p1post/script.d/999update.sh $RPM_BUILD_ROOT/usr/local/p1post/script.d/999update.sh
install -m 0644 usr/local/p1post/state/keep_me $RPM_BUILD_ROOT/usr/local/p1post/state/keep_me

install -m 0644 usr/local/p1post/tmp/keep_me $RPM_BUILD_ROOT/usr/local/p1post/tmp/keep_me

install -m 0755 usr/local/p1post/p1post  $RPM_BUILD_ROOT/usr/local/p1post/p1post

install -m 0755 etc/init.d/p1post $RPM_BUILD_ROOT/etc/init.d/p1post

install -m 0755 etc/sysconfig/iptables $RPM_BUILD_ROOT/etc/sysconfig/iptables

%clean
[ "$RPM_BUILD_ROOT" != "/" ] && %{__rm} -rf $RPM_BUILD_ROOT

%post
  chkconfig --add p1post

%preun
if [ $1 = 0 ]; then
  chkconfig --del p1post
fi

%files
%defattr(-,root,root,-)
%config /etc/init.d/p1post
%config /etc/yum.repos.d/kickstart.repo
%config /etc/sysconfig/iptables
/usr/local/p1post/keys/55BE302B.gpg
/usr/local/p1post/keys/F42584E6.gpg
/usr/local/p1post/lib/config.sh
/usr/local/p1post/lib/keep_me
/usr/local/p1post/lib/p1ks_lib.sh
/usr/local/p1post/logs/keep_me
/usr/local/p1post/script.d/00fixNamed.sh
/usr/local/p1post/script.d/00p1admin.sh
/usr/local/p1post/script.d/10fixphpini.sh
/usr/local/p1post/script.d/15raid.sh
/usr/local/p1post/script.d/20rhn.sh
/usr/local/p1post/script.d/999update.sh
/usr/local/p1post/state/keep_me
/usr/local/p1post/tmp/keep_me
/usr/local/p1post/p1post

%changelog
* Thu May 5 2011 Adam Hubscher <ahubscher@peer1.com> 1.0
- Initial Release
