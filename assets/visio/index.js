const bindEvents = (p) => {
    p.on("signal", (data) => {      //Réception offre
        console.log(JSON.stringify(data))
    })
}

document.getElementById("start").addEventListener("click", () => {
    navigator.mediaDevices.getUserMedia({
        video: true,
        audio: true
    }).then((stream) => {
        const p = new SimplePeer({
            initiator: true,
            stream,
            trickle: false      //Offre sur un seul signal
        })

        bindEvents(p)
        
        const video = document.getElementById("emitter")
        video.srcObject = stream
        video.play()
    }).catch((error) => 
    {
        console.error(error)
    })
})