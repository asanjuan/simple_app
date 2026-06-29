self.addEventListener("install", event => {
    console.log("Service Worker instalado")
})

self.addEventListener("fetch", event => {
    // Aquí puedes cachear recursos si quieres
})