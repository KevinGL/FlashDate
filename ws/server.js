import { WebSocketServer } from "ws";

const wss = new WebSocketServer({ port: 8080 });

const rooms = new Map();

wss.on('connection', (ws) => {
    console.log('Nouveau client connecté !');
    
    ws.on('message', (message) => {
        //console.log(JSON.parse(message));
        const data = JSON.parse(message);
        
        if(data.type === "connect")
        {
            //console.log(data.userId);
            
            const clients = rooms.get(data.uid) ?? new Map();
            clients.set(data.userId, ws);
            
            rooms.set(data.uid, clients);
        }

        else
        if(data.type === "send")
        {
            if(rooms.get(data.uid))
            {
                const clients = rooms.get(data.uid);

                clients.forEach((client, key) => {
                    if (client.readyState === 1)
                    {
                        client.send(JSON.stringify({message: data.message, username: data.username, userId: key}));
                    }
                });
            }
        }
    });
});

console.log("Serveur WebSocket démarré sur ws://localhost:8080");