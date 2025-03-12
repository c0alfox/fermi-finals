# API v1

## Formato input

Qualora richiesto, l'input deve essere prodotto in formato JSON nel corpo della richiesta.

## Autenticazione

Qualora richiesta, l'autenticazione è data da un Token JWT passato in header come Bearer Token. Affinché garantisca l'autenticazione all'utente dedicato, il token deve essere formattato come segue.

### Header

| Claim | Tipo     | Default                             | Richiesto |
| :---: | -------- | ----------------------------------- | :-------- |
| `typ` | `string` | `JWT`                               | ✅*        |
| `alg` | `string` | `HS256`                             | ✅*        |
| `iss` | `string` | `giuseppepappalardo.altervista.org` | N/A**     |
| `iat` | `number` | Timestamp di emissione              | N/A**     |
| `exp` | `number` | Timestamp di scadenza               | ❎         |
\* Il campo è richiesto per conformarsi allo standard [RFC 7519](https://datatracker.ietf.org/doc/html/rfc7519), è assunto come tale ma non è controllato attivamente.
\*\* Il campo non viene controllato attivamente.

### Corpo

| Nome Parametro | Tipo     | Descrizione                                                                | Richiesto |
| :------------: | -------- | -------------------------------------------------------------------------- | --------- |
|    `email`     | `string` | L'email dell'account a cui si è autenticati.                               | ✅         |
| `permissions`  | `number` | Il codice dei permessi descritti nella sezione [[#Gestione dei permessi]]. | ✅         |
### Gestione dei permessi

Un codice di gestione di permessi è una bitmask compreso tra $0$ e $2^6 -1$, con la seguente valenza:

| MSB           |             |          |          | LSB     |
| ------------- | ----------- | -------- | -------- | ------- |
| Cancellazione | Valutazione | Modifica | Commento | Lettura |

## `/account.php`

A questo endpoint si possono svolgere tutte le azioni richieste per la gestione dell'account utente.

### `OPTIONS`

Formato input:

> Nessun input è richiesto dall'utente, qualsiasi informazione inviata sarà scartata.

Formato output:

Il server risponde con codice `204 No Content`; la risposta contiene solo gli header CORS per identificare i metodi disponibili

Risposta di esempio:
```
HTTP/1.1 204 No Content
[...]
Content-Type: application/json; charset=UTF-8
[...]
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: OPTIONS, POST, GET, PUT, DELETE

```

### `POST`

Formato input:

> Input richiesto nel corpo della richiesta in formato JSON.



### `GET`

### `PUT`

### `DELETE`