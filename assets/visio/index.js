const wsURL = document.getElementById('app').getAttribute('wsurl')
const uidClient = document.getElementById('app').getAttribute('uidClient')
const uid = document.getElementById('app').getAttribute('uid')
const initiator = document.getElementById('app').getAttribute('initiator')
const socket = new WebSocket(wsURL)

let p = null

if(initiator)
{
    socket.onopen = () => {
        navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true
        }).then((stream) => {
            p = new SimplePeer({
                initiator: true,
                stream,
                trickle: false      //Offre sur un seul signal
            })

            p.on("signal", (data) => {      //Réception offre
                const offer = JSON.stringify(data)
                socket.send(JSON.stringify({type: 'visio', uid, uidClient, offer, initiator}))
            })
            
            const video = document.getElementById("emitter")
            video.srcObject = stream
            video.play()
        }).catch((error) => 
        {
            console.error(error)
        })

        socket.onmessage = (event) => {
            const msg = JSON.parse(event.data);
            if (msg.type === 'answer' && msg.answer)
            {
                p.signal(msg.answer)

                p.on("stream", (remoteStream) => {
                    const remoteVideo = document.getElementById("receiver")
                    remoteVideo.srcObject = remoteStream
                    remoteVideo.play()
                });
            }
        }
    }
}

else
{
    socket.onopen = () => {
        navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true
        }).then((stream) => {
            socket.send(JSON.stringify({type: 'visio', uid, uidClient, initiator}))
            
            const video = document.getElementById("emitter")
            video.srcObject = stream
            video.play()

            socket.onmessage = (data) => {
                const message = JSON.parse(data.data)
                
                if(message.type === 'offer' && message.offer)
                {
                    p = new SimplePeer({
                        initiator: false,
                        stream,
                        trickle: false
                    })

                    p.on("signal", (data) => {
                        socket.send(JSON.stringify({
                            type: 'visio_answer',
                            uid: uid,
                            uidClient: uidClient,
                            answer: data
                        }))
                    })

                    p.signal(JSON.parse(message.offer))

                    p.on("stream", (remoteStream) => {
                        const remoteVideo = document.getElementById("receiver")
                        remoteVideo.srcObject = remoteStream
                        remoteVideo.play()
                    });
                }
            }
        }).catch((error) => 
        {
            console.error(error)
        })
    }
}

socket.onerror = (err) => console.error('Erreur de connexion :', err);