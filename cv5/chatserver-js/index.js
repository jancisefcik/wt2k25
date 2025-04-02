const WebSocket = require('ws');

const wss = new WebSocket.Server({ port: 8081 });

const clients = [];

wss.on('connection', (ws) => {
    console.log('Client connected');
    clients.push(ws);
    
    ws.on('message', (message) => {
        console.log(`Received message: ${message}`);
        clients.forEach(client => {
        if (client.readyState === WebSocket.OPEN) {
            client.send(message.toString());
        }
        });
    });
    
    ws.on('close', () => {
        console.log('Client disconnected');
        const index = clients.indexOf(ws);
        if (index !== -1) {
            clients.splice(index, 1);
        }
    });

    ws.send('Welcome to the Chat server!');
});

console.log('WebSocket server is running on ws://localhost:8080');