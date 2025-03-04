const fs = require("fs");
const https = require("https");
const WebSocket = require("ws");

// Path to your SSL certificate and private key
const serverOptions = {
  key: fs.readFileSync(
    "/home/Caveni7825/web/caveni.io/public_html/wp-content/plugins/caveni-io/msgsystem/private.key"
  ), // replace with the actual path
  cert: fs.readFileSync(
    "/home/Caveni7825/web/caveni.io/public_html/wp-content/plugins/caveni-io/msgsystem/certificate.crt"
  ), // replace with the actual path
  ca: fs.readFileSync(
    "/home/Caveni7825/web/caveni.io/public_html/wp-content/plugins/caveni-io/msgsystem/ca_bundle.crt"
  ), // if needed (optional, usually for CA bundles)
};

// Create an HTTPS server
const server = https.createServer(serverOptions);

// Create a WebSocket server on top of the HTTPS server
const wss = new WebSocket.Server({ server });

// Handle WebSocket connections
wss.on("connection", (ws) => {
  console.log("New WebSocket connection established");

  // Send a message to the client
  //ws.send("Hello, client! You are connected securely via wss://");

  ws.on("message", (message) => {
    console.log("Received:", message);

    // Broadcast the message to all other connected clients
    wss.clients.forEach((client) => {
      if (client !== ws && client.readyState === WebSocket.OPEN) {
        client.send(message);
      }
    });
  });

  // Handle connection close
  ws.on("close", () => {
    console.log("WebSocket connection closed");
  });

  // Handle errors
  ws.on("error", (error) => {
    console.error("WebSocket error: ", error);
  });
});

// Make the server listen on port 6464
server.listen(6464, () => {
  console.log("Secure WebSocket server is running at wss://caveni.io:6464");
});
