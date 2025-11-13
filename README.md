# AqwSocketClient

PHP client for connecting and interacting with **Adventure Quest Worlds (AQW)** servers, using ReactPHP for asynchronous event handling.

Allows login, sending commands, and processing server events in a modular way.

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
- Modular system based on **Interpreters**, **Translators**, and **Listeners** for event processing and handling.

---

## **Usage**

```php
use AqwSocketClient\Client;
use AqwSocketClient\Server;
use AqwSocketClient\Configuration;
use YourApp\Listeners\CustomGameLogicListener; 

$server = Server::espada();

$config = new Configuration(
    username: 'PlayerName',
    password: 'Password',
    token: 'AuthenticationToken',
    listeners: [new CustomGameLogicListener()], 
    translators: [/* your custom translators here */],
    logMessages: true // Optional: to see raw messages in the console
);

$client = new Client($server, $config);

$promise = $client->connect(); 
```

Checkout **AuthService** class example to generate you auth token
```php
$token = AuthService::getAuthToken('you-user', 'you-pass');
```

---

## ✨ Architecture

The `AqwSocketClient` architecture is based on an **Event-Oriented Pipeline**, ensuring high modularity, strict typing, and **separation of concerns**. The data flow follows the cycle: **Message $\rightarrow$ Interpreter $\rightarrow$ Event $\rightarrow$ Listener/Translator $\rightarrow$ Command**.

---

### **Core Components & Setup**

* **`Client`**: The main class that manages the asynchronous TCP connection (via **ReactPHP**), receives raw data, and orchestrates the processing pipeline.
* **`Configuration`**: An initialization container that stores credentials and registers all essential pipeline components (`Interpreters`, `Translators`, `Listeners`).
* **`Server`**: A value object representing an AQW server (`hostname`, `port`, `name`). Includes **factory methods** for known servers (e.g., `Server::espada()`).
* **`Packet`**: Encapsulates data for sending, automatically appending the mandatory **null terminator (`\u{0000}`)** required by the server protocol.

---

### **I. Input (Raw Messages) - Deserialization**

These classes implement the **`MessageInterface`** and are responsible for deserializing the raw string received from the socket into usable PHP objects, handling the server's multi-protocol nature:

* **`MessageInterface`**: Defines the contract (`fromString()`) to create an object from the raw string.
* **`XmlMessage`**: Handles the **XML format**, converting it into a navigable `DOMDocument`.
* **`JsonMessage`**: Handles the **concatenated JSON format**, pre-processing it into internal `JsonCommand` objects.
* **`DelimitedMessage`**: Handles the **`%`-delimited format**, extracting the message type and payload data.

---

### **II. Processing (Interpretation & Events)**

This stage transforms the decoded raw messages into high-level, strongly-typed events:

* **`InterpreterInterface`**: The contract for classes that convert a `MessageInterface` object into an array of `EventInterface` objects. It acts as the core **parser**.
* **`EventInterface`**: The marker interface for any **event** received and **interpreted** from the server (e.g., `LoginResponseEvent`, `PlayerDetectedEvent`).

---

### **III. Output & Logic (Responses and Actions)**

The interpreted event is dispatched to two types of components, allowing the application to react in a decoupled manner:

#### **A. Application Logic (Listeners)**

* **`ListenerInterface`**: Executes **application logic** in response to an event (e.g., logging a detected player, updating internal state), **without generating commands** to the server.

#### **B. Server Responses (Translators)**

* **`TranslatorInterface`**: Converts an `EventInterface` into a `CommandInterface` (response command) if a direct action is required. For instance, the **`LoginTranslator`** converts the `ConnectionEstabilishedEvent` into the `LoginCommand`.

---

### **IV. Output (Commands) - Serialization**

* **`CommandInterface`**: Defines a **command** or **action** to be sent back to the server.
* The required method `pack()` serializes the command into the necessary protocol format (XML, Delimited, etc.) and wraps it in a **`Packet`** ready for transmission.
* Examples: `LoginCommand`, `FirstLoginCommand`.