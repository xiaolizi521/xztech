function set_nav(type,args) {
    try {
        menu_type = top.frames.nav.menu_type;
    } catch(e) {
        return;
    }
    if ( type == 'none') {
        if( top.frames.nav.menu_type != 'none' ) {
            loc = "/CORE_menu.php?menu_type="+type;
            top.frames.nav.location.href = loc;
        }
    } else {
        if( top.frames.nav.menu_type != type ||
            top.frames.nav.menu_args != args ) {
            loc = "/CORE_menu.php" + "?action=" + type + "&" + args +
                "&menu_type="+type+"&menu_args="+escape(args);
            top.frames.nav.location.href = loc;
        }
    }
}

// Local Variables:
// mode: java
// End:
