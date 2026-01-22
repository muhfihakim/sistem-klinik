const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    delay,
} = require("@whiskeysockets/baileys");
const { Boom } = require("@hapi/boom");
const express = require("express");
const qrcode = require("qrcode-terminal");
const fs = require("fs");

const app = express();
app.use(express.json());

// CONFIGURATION
const PORT = 3000;
const API_KEY = "S1KL1N1K_S3CUR3_K3Y";

// Variabel Global untuk menyimpan state
let sock;
let lastQr = null;
let isConnected = false;

async function startWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState("auth_info");

    sock = makeWASocket({
        auth: state,
        printQRInTerminal: true,
        browser: ["SIKLINIK Gateway", "MacOS", "3.0.0"],
        syncFullHistory: false,
    });

    sock.ev.on("creds.update", saveCreds);

    sock.ev.on("connection.update", async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) lastQr = qr;

        if (connection === "close") {
            isConnected = false;
            const statusCode =
                lastDisconnect.error instanceof Boom
                    ? lastDisconnect.error.output.statusCode
                    : null;

            console.log("Koneksi terputus, status code:", statusCode);

            // Jika Unauthorized atau Logged Out (401)
            if (
                statusCode === DisconnectReason.loggedOut ||
                statusCode === 401
            ) {
                console.log("Sesi tidak valid. Menghapus folder auth_info...");
                lastQr = null;

                // Hapus folder sesi agar memicu QR baru
                if (fs.existsSync("./auth_info")) {
                    fs.rmSync("./auth_info", { recursive: true, force: true });
                }

                // Beri jeda sedikit sebelum restart
                setTimeout(() => startWhatsApp(), 2000);
            } else {
                // Reconnect untuk error selain logout
                startWhatsApp();
            }
        } else if (connection === "open") {
            isConnected = true;
            lastQr = null;
            console.log("WhatsApp SIKLINIK Terhubung! ✅");
        }
    });

    // --- ENDPOINT UNTUK DASHBOARD LARAVEL ---

    // 1. Endpoint Cek Status & Ambil QR
    app.get("/status", (req, res) => {
        const clientApiKey = req.headers["x-api-key"];
        if (clientApiKey !== API_KEY)
            return res.status(403).json({ error: "Forbidden" });

        // Ambil nomor saja dari ID (contoh: 62812345678@s.whatsapp.net -> 62812345678)
        let phoneNumber = null;
        if (isConnected && sock.user) {
            phoneNumber = sock.user.id.split(":")[0].split("@")[0];
        }

        res.json({
            status: isConnected
                ? "connected"
                : lastQr
                ? "disconnected"
                : "loading",
            user: isConnected
                ? {
                      id: sock.user.id,
                      name: phoneNumber, // Sekarang isinya nomor telepon saja
                  }
                : null,
            qr: lastQr,
        });
    });

    // 2. Endpoint Logout
    app.post("/logout", async (req, res) => {
        const clientApiKey = req.headers["x-api-key"];
        if (clientApiKey !== API_KEY)
            return res.status(403).json({ error: "Forbidden" });

        try {
            await sock.logout();
            res.json({ status: "Success", message: "Logged out" });
        } catch (err) {
            res.status(500).json({ status: "Error", message: err.message });
        }
    });

    // 3. Endpoint Kirim Pesan (Tetap sama)
    app.post("/send-message", async (req, res) => {
        const { number, message } = req.body;
        const clientApiKey = req.headers["x-api-key"];

        if (clientApiKey !== API_KEY)
            return res.status(403).json({ error: "Forbidden" });

        try {
            const jid = number.replace(/\D/g, "") + "@s.whatsapp.net";
            await sock.sendPresenceUpdate("composing", jid);
            await delay(Math.floor(Math.random() * 3000) + 2000);
            await sock.sendPresenceUpdate("paused", jid);
            await sock.sendMessage(jid, { text: message });

            res.json({ status: "Success", message: "Pesan terkirim" });
        } catch (err) {
            res.status(500).json({ status: "Error", message: err.message });
        }
    });
}

startWhatsApp();
app.listen(PORT, () => console.log(`WA Service running on port ${PORT}`));
