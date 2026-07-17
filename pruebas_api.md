## 1. Discovery — `GET /api/v1`

```bash
curl -s https://filmatrix.edutokens.xyz/api/v1 | jq .
```
---

## 2. Autenticación por token

### 2.1 Emitir token

```bash
curl -s -X POST https://filmatrix.edutokens.xyz/api/v1/auth/tokens \
  -H "Content-Type: application/json" \
  -d '{"email": "<email>", "password": "<password>", "label": "<label>"}' | jq .
```

### 2.2 Listar tokens propios

```bash
curl -s https://filmatrix.edutokens.xyz/api/v1/auth/tokens \
  -H "Authorization: Bearer <token>" | jq .
```

### 2.3 Revocar un token

```bash
curl -s -X DELETE https://filmatrix.edutokens.xyz/api/v1/auth/tokens \
  -H "Authorization: Bearer <token>" -H "Content-Type: application/json" \
  -d '{"id": <token_id>}' | jq .
```

---

## 3. Recurso Reviews

### 3.1 Crear reseña

```bash
curl -s -X POST https://filmatrix.edutokens.xyz/api/v1/reviews \
  -H "Authorization: Bearer <token>" -H "Content-Type: application/json" \
  -d '{"title_id": <title_id>, "score": <score>, "body": "<body>"}' | jq .
```

### 3.2 Listar reseñas propias

```bash
curl -s https://filmatrix.edutokens.xyz/api/v1/reviews \
  -H "Authorization: Bearer <token>" | jq .
```

### 3.3 Ver una reseña puntual

```bash
curl -s "https://filmatrix.edutokens.xyz/api/v1/reviews?id=<review_id>" \
  -H "Authorization: Bearer <token>" | jq .
```

### 3.4 Actualizar reseña

```bash
curl -s -X PATCH https://filmatrix.edutokens.xyz/api/v1/reviews \
  -H "Authorization: Bearer <token>" -H "Content-Type: application/json" \
  -d '{"id": <review_id>, "score": <score>, "body": "<body>"}' | jq .
```

### 3.5 Borrar reseña

```bash
curl -s -X DELETE https://filmatrix.edutokens.xyz/api/v1/reviews \
  -H "Authorization: Bearer <token>" -H "Content-Type: application/json" \
  -d '{"id": <review_id>}' | jq .
```

---

## 4. Recurso Watchlist

### 4.1 Agregar título a la watchlist

```bash
curl -s -X POST https://filmatrix.edutokens.xyz/api/v1/watchlist \
  -H "Authorization: Bearer <token>" -H "Content-Type: application/json" \
  -d '{"title_id": <title_id>}' | jq .
```

### 4.2 Listar watchlist propia

```bash
curl -s https://filmatrix.edutokens.xyz/api/v1/watchlist \
  -H "Authorization: Bearer <token>" | jq .
```

```bash
# filtrada por status.
curl -s "https://filmatrix.edutokens.xyz/api/v1/watchlist?status=pending" \
  -H "Authorization: Bearer <token>" | jq .
```

### 4.3 Ver un item puntual

```bash
curl -s "https://filmatrix.edutokens.xyz/api/v1/watchlist?title_id=<title_id>" \
  -H "Authorization: Bearer <token>" | jq .
```

### 4.4 Actualizar status

```bash
curl -s -X PATCH https://filmatrix.edutokens.xyz/api/v1/watchlist \
  -H "Authorization: Bearer <token>" -H "Content-Type: application/json" \
  -d '{"title_id": <title_id>, "status": "watched"}' | jq .
```

### 4.5 Borrar item

```bash
curl -s -X DELETE https://filmatrix.edutokens.xyz/api/v1/watchlist \
  -H "Authorization: Bearer <token>" -H "Content-Type: application/json" \
  -d '{"title_id": <title_id>}' | jq .
```

---
