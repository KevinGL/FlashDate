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
            const clients = rooms.get(data.uid) ?? [];
            const client = {ws, id: data.userSlug, username: data.username};
            
            const index = clients.findIndex((c) => {
                return c.id === data.userSlug
            });

            if(index === -1)
            {
                clients.push(client);
            }
            else
            {
                clients[index] = client;
            }
            
            rooms.set(data.uid, clients);
        }

        else
        if(data.type === "send")
        {
            if(rooms.get(data.uid))
            {
                const clients = rooms.get(data.uid);
                const indexAuthor = clients.findIndex((c) => {
                    return c.id === data.userSlug;
                });

                clients.forEach((client) => {
                    if (client.ws.readyState === 1)
                    {
                        client.ws.send(JSON.stringify({message: data.message, username: clients[indexAuthor].username}));
                    }
                });
            }
        }
    });
});

console.log("Serveur WebSocket démarré sur ws://localhost:8080");