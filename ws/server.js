import { WebSocketServer } from "ws";

const wss = new WebSocketServer({ port: 8080 });

const rooms = new Map();
const daties = new Map();

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

        else
        if(data.type === "visio")
        {
            const clients = daties.get(data.uid) ?? [];
            const client = {ws, uid: data.uid, uidClient: data.uidClient, offer: data.offer ?? "", initiator: data.initiator};
            
            const index = clients.findIndex((c) => {
                return c.uidClient === data.uidClient
            });

            if(index === -1)
            {
                clients.push(client);
            }
            else
            {
                clients[index] = client;
            }

            daties.set(data.uid, clients);
            
            if(clients.length === 2)        //Échange offres
            {
                if(clients[0].initiator && !clients[1].initiator)
                {
                    clients[1].ws.send(JSON.stringify({type: "offer", offer: clients[0].offer}));
                }

                else
                if(!clients[0].initiator && clients[1].initiator)
                {
                    clients[0].ws.send(JSON.stringify({type: "offer", offer: clients[1].offer}));
                }
            }
        }

        else
        if(data.type === "visio_answer")
        {
            const clients = daties.get(data.uid);

            if(clients)
            {
                const client = clients[0].uidClient !== data.uidClient ? clients[0] : clients[1];
                client.ws.send(JSON.stringify({type: "answer", answer: data.answer}));
            }
        }
    });
});

console.log("Serveur WebSocket démarré sur ws://localhost:8080");