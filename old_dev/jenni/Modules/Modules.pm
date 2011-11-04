# $Id: Modules.pm,v 1.8 2002/03/03 22:21:57 jenni Exp $
package Modules;

use Libraries::Util;

sub init {
	my ($db) = $::db;
	$sth = $db->query("SELECT module FROM modules") || next;
	while ($module = $sth->fetchrow) {
		&loadmodule($module);
	}
	&::addhandler("module",  \&module, 1);
	&::addhandler("modules", \&modulelist);
	&::addhandler("load",    \&load);
	&::addhandler("reload",  \&load);
	&::addhandler("unload",  \&unload);
	return 0;
}

sub destruct {
	&::delhandler("module");
	&::delhandler("modules");
	&::delhandler("load");
	&::delhandler("reload");
	&::delhandler("unload");
	return 0;
}

sub loadmodule {
	my ($module) = @_;
	print "Loading module $module\n" if $::options{"debug"} == 1;
	$retval = eval { do "Modules/$module.pm"; };
	if (LibUtil::is_loaded_module($module)) {
		if ($retval == undef) {
			splice(@::modules, LibUtil::moduleindex($module), 1);
			return -2;
		} else {
			$module->init() if &::can($module, "init");
			return 2;
		}
	} else {
		if ($retval == undef) {
			return -1;
		} else {
			push(@::modules, $module);
			$module->init() if &::can($module, "init");
			return 1;
		}
	}
}

sub unloadmodule {
	my ($module) = @_;
	print "Unloading module $module\n" if $::options{"debug"} == 1;
	if (LibUtil::is_loaded_module($module)) {
		splice(@::modules, LibUtil::moduleindex($module), 1);
		$module->destruct() if &::can($module, "destruct");
		return 1;
	} else {
		return -1;
	}
}

sub load {
	my ($self, $event, $to, $module) = @_;
	return if !LibUtil::is_admin($event->userhost);
	$rv = &loadmodule($module);
	$message = $event->nick . ": ";
	if ($rv == 1) {
		$message .= "Module $module loaded.";
	} elsif ($rv == 2) {
		$message .= "Module $module reloaded.";
	} elsif ($rv == -1) {
		$message .= "Error in module $module, load halted.";
	} elsif ($rv == -2) {
		$message .= "Error in module $module, disabling.";
	}
	$self->privmsg($to, $message);
}

sub unload {
	my ($self, $event, $to, $module) = @_;
	return if !LibUtil::is_admin($event->userhost);
	$rv = &unloadmodule($module);
	$message = $event->nick . ": ";
	if ($rv == 1) {
		$message .= "Module $module unloaded.";
	} else {
		$message .= "Error, no such module loaded.";
	}
	$self->privmsg($to, $message);
}

sub module {
	my ($self, $event, $to, $module, $plusminus) = @_;
	my ($db) = $::db;
	return if !LibUtil::is_admin($event->userhost);
	$module = LibUtil::stripforshell($module);
	if ($plusminus eq '+') {
		$rv = &loadmodule($module);
		$message = $event->nick . ": ";
		if ($rv == 1) {
			$message .= "Module $module loaded.";
			$db->query("INSERT INTO modules (module, userid) VALUES ('$module', '" . LibUtil::userid($event) . "')") || next;
		} elsif ($rv == 2) {
			$message .= "Module $module reloaded.";
		} elsif ($rv == -1) {
			$message .= "Error in module $module, load halted.";
		} elsif ($rv == -2) {
			$message .= "Error in module $module, disabling.";
		}
		$self->privmsg($to, $message);
	}
	elsif ($plusminus eq '-') {
		$rv = &unloadmodule($module);
		$message = $event->nick . ": ";
		if ($rv == 1) {
			$message .= "Module $module unloaded.";
			$db->query("DELETE FROM modules WHERE module = '$module'") || next;
		} else {
			$message .= "Error, no such module loaded.";
		}
		$self->privmsg($to, $message);
	}
	elsif (lc($module) eq 'list') {
		&modulelist($self, $event, $to);
	}
}

sub modulelist {
	my ($self, $event, $to, $module) = @_;
	return if !LibUtil::is_admin($event->userhost);
	undef($modlist);
	for (@::modules) { $modlist .= $_ . " "; }
	$self->privmsg($to, $event->nick . ": Currently loaded modules: $modlist");
}

1;
