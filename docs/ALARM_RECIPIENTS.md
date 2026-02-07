# Sistema de Destinatarios de Alarmas

## Descripción General

El sistema de alarmas ahora soporta múltiples destinatarios de correo electrónico por tipo de alarma. Cada cliente puede configurar diferentes direcciones de correo para recibir notificaciones según el tipo de alerta:

- **Stock** (ID 1): Alertas cuando el stock está por debajo del mínimo
- **Horario** (ID 2): Alertas por actividad fuera del horario comercial  
- **Holiday** (ID 3): Alertas por actividad en días festivos (NUEVO)

## API REST Endpoints

### GET /api/alarm-recipients
Lista destinatarios. Query params: `uuidClient` (requerido), `alarmType` (opcional)

### POST /api/alarm-recipients
Crea destinatario. Body: `{uuidClient, alarmTypeId, email}`

### DELETE /api/alarm-recipients/{id}
Elimina destinatario. Query param: `uuidClient`

## Migración

```bash
php migrations/client/migrate_client.php
```

La migración 026:
- Añade tipo de alarma "holiday"
- Crea tabla alarm_type_recipients
- Copia company_email como destinatario inicial

## Testing Manual Recomendado

1. Ejecutar migración
2. Verificar destinatarios iniciales
3. Probar API endpoints
4. Disparar alarmas y verificar envíos múltiples
