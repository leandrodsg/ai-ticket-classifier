# Priority System (ITIL-based)

This document describes the implementation of an ITIL-based ticket prioritization system with automatic SLA calculation for the AI Ticket Classifier application.

## Current State Analysis

### Database Structure
The `tickets` table currently has the following fields:
- `id` (primary key)
- `title` (string)
- `description` (text)
- `category` (string, nullable) - AI-classified
- `sentiment` (string, nullable) - AI-classified
- `confidence` (decimal 3,2, nullable) - AI confidence score
- `status` (string, default 'open')
- `ai_classification_log` (json, nullable)
- `created_at`, `updated_at`, `deleted_at` (timestamps + soft deletes)

### AI Classification Service
The `TicketClassifierService` currently:
- Classifies tickets into categories: `technical`, `commercial`, `billing`, `general`, `support`
- Analyzes sentiment: `positive`, `negative`, `neutral`
- Returns confidence score (0.0-1.0)
- Supports both real AI (OpenRouter) and mock classification

### Controller Integration
The `TicketController::store()` method:
- Creates ticket from validated request
- Calls `TicketClassifierService::classify()` with ticket description
- Updates ticket with `category`, `sentiment`, `confidence`
- Logs classification to `AiLog` table

## ITIL Priority Matrix

Priority is calculated using the standard ITIL Impact × Urgency matrix:

| Impact \ Urgency | High Urgency | Medium Urgency | Low Urgency |
|------------------|--------------|----------------|-------------|
| **Critical Impact** | Critical | Critical | High |
| **High Impact** | Critical | High | Medium |
| **Medium Impact** | High | Medium | Low |
| **Low Impact** | Medium | Low | Low |

### Category to Impact Mapping
- `technical` → Critical Impact
- `billing` → High Impact
- `commercial` → Medium Impact
- `general`, `support` → Low Impact

### Sentiment to Urgency Mapping
- `negative` → High Urgency
- `neutral` → Medium Urgency
- `positive` → Low Urgency

## SLA Definitions

SLAs are defined per priority level:
- **Critical**: 1 hour
- **High**: 4 hours
- **Medium**: 24 hours
- **Low**: 48 hours

## New Database Fields

The following fields will be added to the `tickets` table:
- `priority` (string/enum) - Calculated priority level
- `sla_due_at` (timestamp, nullable) - SLA deadline
- `impact_level` (string, nullable) - For auditing
- `urgency_level` (string, nullable) - For auditing
- `escalated_at` (timestamp, nullable) - Manual escalation timestamp

## Business Rules

### Automatic Priority Recalculation
When a ticket is updated and the description changes, the system will automatically:
1. Re-classify category and sentiment
2. Recalculate priority
3. Update SLA due date

### Manual Escalation
Tickets can be manually escalated by setting `escalated_at`, which overrides automatic priority calculation.

### SLA Validation
All Impact × Urgency combinations must result in a valid priority level. Invalid combinations will throw exceptions.

## Testing Strategy

- Unit tests for PriorityCalculatorService
- Unit tests for SlaCalculatorService
- Integration tests for controller updates
- Feature tests for UI components
- End-to-end testing of complete priority flow

## References

- ITIL v4 Service Management practices
- ISO/IEC 20000-1:2018 standard
- Enterprise service desk best practices
