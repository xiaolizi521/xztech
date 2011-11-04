
function ticket_tree_show_info( id ) {
    for (var i=0; i < ticket_tree_info_list.length; i++) {
        var lid = ticket_tree_info_list[i];
        var div = document.getElementById( lid );
        var hilite = document.getElementById( lid+"_highlite" );
        if( lid == id ) {
            div.style.display = 'block';
            hilite.style.border = 'solid thin black';
            if( ticket_tree_eval_list[i] ) {
                eval( ticket_tree_eval_list[i] );
                ticket_tree_eval_list[i] = "";
            }
            /* Color the panel */
            var panel = document.getElementById( 'ticket_tree_info_panel' );
            panel.style.background = ticket_tree_panel_list[i];
        } else {
            div.style.display = 'none';
            hilite.style.border = 'none';
        }
    }
}

// Local Variables:
// mode: java
// End:
