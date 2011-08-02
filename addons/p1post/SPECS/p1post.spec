# File: p1post.spec
# Package: p1post
# Install Location: None (used for building RPM)
# Name: P1POST RPMBUILD SPEC FILE
#
# Supported Platforms: 
# Redhat Enterprise Linux Based Distributions (Fedora, CentOS, RHEL)
#
# Description:
# This file facilitates the building of the p1post RPM for inclusion into any
# redhat system.
#
# Usage:
# Under the root p1post development directory, you will find a script: "build.sh"
# Running this script will build the p1post RPM based upon this SPEC file.
#
# Author: Adam Hubscher <ahubscher AT peer1 DOT com>
# Version: 1.0
# Last Updated: N/A
# Revision: 1

## Variables ##

# Define the p1post directory structure
%define _root_dir    /usr/local/p1post
%define _script_dir  %_root_dir/script.d
%define _file_dir    %_root_dir/files
%define _log_dir     %_root_dir/logs
%define _state_dir   %_root_dir/state
%define _tmp_dir     %_root_dir/tmp
%define _lib_dir     %_root_dir/lib
%define _key_dir     %_root_dir/keys

# Define the etc directory structure
%define _yum_dir     /etc/yum.repos.id
%define _init_dir    /etc/init.d


## Begin SPEC Definition Header ##
Summary:    Peer1 Post Installation Routines
Name:       p1post
Version:    1.0
Release:    1
License:    GPL
ExclusiveOS: Linux
Group:      System Environment/Base
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-build
BuildArch:  noarch
Source0:    %{name}-%version.tar.gz

%description
The p1post utility facilitiates the execution of any tasks following the installation of the operating system.

## RPM SPEC Build Directives ##
%prep
%setup

### Begin p1post Installation ###

%install
## Note: This is a shell script. All commands should be executable from a BASH prompt. ##

### Directory Tree Creation ###
# Create the p1post directory tree.
install -m 0755 -d $RPM_BUILD_ROOT/%_root_dir
install -m 0755 -d $RPM_BUILD_ROOT/%_lib_dir
install -m 0755 -d $RPM_BUILD_ROOT/%_script_dir
install -m 0755 -d $RPM_BUILD_ROOT/%_log_dir
install -m 0755 -d $RPM_BUILD_ROOT/%_state_dir
install -m 0755 -d $RPM_BUILD_ROOT/%_tmp_dir
install -m 0755 -d $RPM_BUILD_ROOT/%_key_dir
install -m 0755 -d $RPM_BUILD_ROOT/%_file_dir

# Create the /etc/ directory tree.
install -m 0755 -d $RPM_BUILD_ROOT/etc
install -m 0755 -d $RPM_BUILD_ROOT/%_init_dir
install -m 0755 -d $RPM_BUILD_ROOT/%_yum_dir

# Create the yum repo directory tree.
install -m 0755 yum.repos.d/kickstart.repo $RPM_BUILD_ROOT/%_yum_dir/kickstart.repo

### File Installation ###
# Copy the GPG keys into place.
install -m 0644 usr/local/p1post/keys/55BE302B.gpg $RPM_BUILD_ROOT/%_key_dir/55BE302B.gpg
install -m 0644 usr/local/p1post/keys/F42584E6.gpg $RPM_BUILD_ROOT/%_key_dir/F42584E6.gpg

# Copy the p1post libs into place.
install -m 0755 usr/local/p1post/lib/config.sh $RPM_BUILD_ROOT/%_lib_dir/config.sh
install -m 0755 usr/local/p1post/lib/p1ks_lib.sh $RPM_BUILD_ROOT/%_lib_dir/p1ks_lib.sh

# Copy the permanent modules into place.
install -m 0755 usr/local/p1post/script.d/00fixNamed.sh $RPM_BUILD_ROOT/%_script_dir/00fixNamed.sh
install -m 0755 usr/local/p1post/script.d/00p1admin.sh $RPM_BUILD_ROOT/%_script_dir/00p1admin.sh
install -m 0755 usr/local/p1post/script.d/10fixphpini.sh $RPM_BUILD_ROOT/%_script_dir/10fixphpini.sh
install -m 0755 usr/local/p1post/script.d/10fixphpini.sh $RPM_BUILD_ROOT/%_script_dir/11firewall.sh
install -m 0755 usr/local/p1post/script.d/15raid.sh $RPM_BUILD_ROOT/%_script_dir/15raid.sh
install -m 0755 usr/local/p1post/script.d/20rhn.sh $RPM_BUILD_ROOT/%_script_dir/20rhn.sh
install -m 0755 usr/local/p1post/script.d/999update.sh $RPM_BUILD_ROOT/%_script_dir/999update.sh
install -m 0755 usr/local/p1post/script.d/999mh.sh $RPM_BUILD_ROOT/%_script_dir/999mh.sh

# Copy any files used by p1post into place.
install -m 0755 usr/local/p1post/files/iptables $RPM_BUILD_ROOT/%_file_dir/iptables

# Copy the primary p1post execution script into place.
install -m 0755 usr/local/p1post/p1post  $RPM_BUILD_ROOT/%_root_dir/p1post

# Copy the init script into place
install -m 0755 etc/init.d/p1post $RPM_BUILD_ROOT/%_init_dir/p1post

### Done p1post Installation ###

%clean
[ "$RPM_BUILD_ROOT" != "/" ] && %{__rm} -rf $RPM_BUILD_ROOT

## Begin Post Installation Script ##
%post

# Add p1post to the startup of the system
chkconfig --add p1post

## End Post Installation Script ##

## Begin Pre-Uninstall Script ##
%preun

# If we are uninstalling, we want to remove it from startup.

if [ $1 = 0 ]; then
  chkconfig --del p1post
fi

## End Pre-Uninstall Script ##

## Begin Files List ##

%files

# Set up the attributes we'd like to use
%defattr(-,root,root,-)

# Define config file locations
%config %_init_dir/p1post
%config %_yum_dir/kickstart.repo

# Define p1post Directory Tree
%dir %_root_dir
%dir %_script_dir
%dir %_file_dir
%dir %_log_dir
%dir %_state_dir
%dir %_tmp_dir
%dir %_lib_dir
%dir %_key_dir

# Define p1post File Tree
%_file_dir/iptables
%_key_dir/55BE302B.gpg
%_key_dir/F42584E6.gpg
%_lib_dir/config.sh
%_lib_dir/p1ks_lib.sh
%_script_dir/00fixNamed.sh
%_script_dir/00p1admin.sh
%_script_dir/10fixphpini.sh
%_script_dir/11firewall.sh
%_script_dir/15raid.sh
%_script_dir/20rhn.sh
%_script_dir/999update.sh
%_script_dir/999mh.sh
%_root_dir/p1post


## End Files List ##

## Begin Changelog ##
%changelog
* Thu May 5 2011 Adam Hubscher <ahubscher@peer1.com> 1.0
- Initial Release