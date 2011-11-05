/* Dynamic Queue Buttons */

function updateButtons() {
  // Written by Justin
  var queue_select = document.getElementById('queue_select');
  var queue_val = queue_select.value;

  var view_button = document.getElementById('view_button');
  view_button.href = '/py/ticket/queue.pt?open=' + queue_val;

  var monitor_button = document.getElementById('monitor_button');
  monitor_button.href = '/py/ticket/monitor.pt?monitor=' + queue_val;
  monitor_button.onclick = "doPopUp('/py/ticket/monitor.pt?monitor=" + queue_val + "','TicketMonitor" + queue_val + "',960,400,'status,scrollbars,resizable'); return false;";
  monitor_button.target = 'monitor' + queue_val;
}
