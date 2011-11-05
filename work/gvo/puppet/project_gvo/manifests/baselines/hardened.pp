class hardened {
# A baseline class with hardening.
# READ CAREFULLY and uncomment with counsciousness
# Includes minimal or general base settings.
        include minimal
#       include general

# Iptables enabled. CONFIGURE them!
        include iptables::enable
# Some sysctl tuning & hardening
        include sysctl::hardened

# Some uncomfortable eal4 oriented hardenings. LOOK at the class to understand consequences.
#       include hardening::eal4
# If you include "hardening::eal4", you must add users. Uncomment to create en gvo user.
# Customize  or CHANGE DEFAULT PASSWORD: gvo !!
#       include users::gvo

# SELINUX management. Include selinux::disabled , selinux::permissive or selinux::enforcing
#       include selinux::enforcing

}

