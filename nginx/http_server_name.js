var server_name = '-';

/**
 * Read the server name from the HTTP stream.
 *
 * @param s
 *   Stream.
 */
function read_server_name(s) {
  s.on('upload', function (data, flags) {
    if (data.length || flags.last) {
      s.done();
    }

    // If we can find the Host header.
    var n = data.indexOf('\r\nHost: ');
    if (n == -1){ n = data.indexOf('\r\nhost: '); }
    if (n != -1) {
      // Determine the start of the Host header value and of the next header.
      var start_host = n + 8;
      var next_header = data.indexOf('\r\n', start_host);

      // Extract the Host header value.
      server_name = data.substr(start_host, next_header - start_host);

      // Remove the port if given.
      var port_start = server_name.indexOf(':');
      if (port_start != -1) {
        server_name = server_name.substr(0, port_start);
      }
    }
  });
}

function get_server_name(s) {
  s.log(server_name);
  return server_name;
}

export default {read_server_name, get_server_name}