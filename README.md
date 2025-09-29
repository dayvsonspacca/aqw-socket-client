# AqwSocketClient

PHP client for connecting and interacting with **Adventure Quest Worlds (AQW)** servers, using ReactPHP for asynchronous event handling.

Allows login, sending commands, and processing server events in a modular way.  

**Note:** Once connected, the client cannot cancel the connection on its own; it will remain active until the server closes it, the script is terminated manually, or you create a `Command` to exit the program.

**Note:** This client is not intended to serve as a bot for the game. Its purpose is solely to explore the exchange of information with the server and to obtain data such as item names and player information.

**Note:** This project would not have been possible without the following repositories, thank you!

- [anthony-hyo/swf2png](https://github.com/anthony-hyo/swf2png)  
- [dwiki08/loving](https://github.com/dwiki08/loving)  
- [Froztt13/aqw-python](https://github.com/Froztt13/aqw-python)  
- [BrenoHenrike/RBot](https://github.com/BrenoHenrike/RBot)  
- [133spider/AQLite](https://github.com/133spider/AQLite)


---

## **Features**

- Creation and sending of commands to the server.  
- Processing of server messages as events.  
- Modular system of event factories and handlers.

---

## **Installation**

```bash
composer require dayvsonspacca/aqw-socket-client:dev-main
```

---

## **Usage**

```php
$server = \AqwSocketClient\Server::espada();

$client = new \AqwSocketClient\Client(
    $server,
    [new \AqwSocketClient\Events\Factories\CoreEventsFactory()],
    [new \AqwSocketClient\Events\Handlers\CoreEventsHandler('PlayerName', 'Token')]
);

$client->run();
```
> See my other project [dayvsonspacca/aq-hub](https://github.com/dayvsonspacca/aq-hub) `AqwApiClient` file to see how generate you token.
---

## **Architecture**

### **Server**

- **Server**: Represents an AQW server with hostname and port.  
  - Factory methods for known servers like (`espada()`).

### **Packets**

- **Packet**: Encapsulates data for sending to the server.  
  - `packetify(string $data)` — creates a packet with null terminator.  
  - `unpacketify()` — returns the packet data.  
- **PacketException**: Exception for packet errors.

### **Client**

- **Client**: Manages the TCP connection, sending commands, and event processing.  
  - `run()` — starts the event loop and connects to the server.  
  - `send(CommandInterface $command)` — sends a command.  
  - Incoming messages are processed through factories and handlers.

### **Events**

- **EventInterface** — marker for any AQW client event.  
- **RawMessageEvent** — generic event for each raw message received.  
- **LoginSuccessfulEvent** — triggered when login succeeds.  
- **ConnectionEstabilishedEvent** — triggered when the connection is established.

### **Factories and Handlers**

- **EventsFactoryInterface** — converts raw messages into events.  
  - `CoreEventsFactory` creates core events: raw message, connection, login.  
- **EventsHandlerInterface** — handles events and returns commands.  
  - `CoreEventsHandler` sends `LoginCommand` and `AfterLoginCommand` according to events.

### **Commands**

- **CommandInterface** — defines a command to send to the server.  
  - `toPacket()` returns a `Packet`.  
- **LoginCommand** — login command.  
- **AfterLoginCommand** — command sent after login.
