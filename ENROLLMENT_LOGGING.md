# Enrollment Logging System

This document describes the enrollment logging system that tracks changes to student enrollments.

## Overview

The enrollment logging system automatically tracks changes to enrollment records, including:
- Grade level changes
- Status changes (active, transferred, graduated, withdrawn)
- Classroom assignments
- Academic year changes
- Discount changes

## Database Schema

### `enrollment_logs` Table

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `enrollment_id` | bigint | Foreign key to enrollments table |
| `student_id` | bigint | Foreign key to students table |
| `user_id` | bigint | Foreign key to users table (who made the change) |
| `action_type` | string | Type of action (grade_level_change, status_change, etc.) |
| `field_name` | string | Name of the field that was changed |
| `old_value` | text | Previous value |
| `new_value` | text | New value |
| `description` | text | Human-readable description of the change |
| `metadata` | json | Additional context data |
| `changed_at` | timestamp | When the change occurred |
| `created_at` | timestamp | When the log entry was created |
| `updated_at` | timestamp | When the log entry was last updated |

## Usage

### Automatic Logging

The system automatically logs changes when enrollments are updated through the API:

```php
// When updating an enrollment, changes are automatically logged
PUT /api/enrollments/{id}
{
    "grade_level_id": 2,
    "status": "active"
}
```

### Manual Logging

You can also manually create log entries:

```php
use App\Models\EnrollmentLog;

EnrollmentLog::logChange(
    enrollmentId: $enrollment->id,
    studentId: $enrollment->student_id,
    actionType: 'grade_level_change',
    fieldName: 'grade_level_id',
    oldValue: '1',
    newValue: '2',
    description: 'Grade level changed from Grade 1 to Grade 2',
    metadata: [
        'old_grade_level_name' => 'Grade 1',
        'new_grade_level_name' => 'Grade 2',
        'academic_year' => '2025/2026',
        'school_id' => $enrollment->school_id,
    ]
);
```

## API Endpoints

### Get Enrollment Logs

```http
GET /api/enrollments/{enrollment}/logs
```

Returns all log entries for a specific enrollment.

**Response:**
```json
{
    "data": [
        {
            "id": 1,
            "enrollment_id": 1,
            "student_id": 1,
            "user_id": 1,
            "action_type": "grade_level_change",
            "field_name": "grade_level_id",
            "old_value": "1",
            "new_value": "2",
            "description": "تم تغيير المرحلة الدراسية من \"Grade 1\" إلى \"Grade 2\"",
            "metadata": {
                "old_grade_level_name": "Grade 1",
                "new_grade_level_name": "Grade 2",
                "academic_year": "2025/2026",
                "school_id": 1
            },
            "changed_at": "2025-01-11T18:39:07.000000Z",
            "user": {
                "id": 1,
                "name": "Admin User"
            }
        }
    ],
    "enrollment": {
        // Enrollment details
    }
}
```

### Get Student Enrollment Logs

```http
GET /api/students/{student}/enrollment-logs
```

Returns all enrollment log entries for a specific student across all their enrollments.

## Action Types

| Action Type | Description | Arabic Label |
|-------------|-------------|--------------|
| `grade_level_change` | Grade level was changed | تغيير المرحلة الدراسية |
| `status_change` | Enrollment status was changed | تغيير حالة التسجيل |
| `classroom_change` | Classroom assignment was changed | تغيير الفصل الدراسي |
| `fees_change` | Fees were changed | تغيير الرسوم |
| `discount_change` | Discount was changed | تغيير الخصم |
| `academic_year_change` | Academic year was changed | تغيير العام الدراسي |

## Model Relationships

The `EnrollmentLog` model has the following relationships:

- `enrollment()` - Belongs to an Enrollment
- `student()` - Belongs to a Student
- `user()` - Belongs to a User (who made the change)

## Error Handling

The logging system is designed to be non-intrusive. If logging fails, it will:
1. Log the error to the application log
2. Continue with the enrollment update
3. Not fail the main operation

## Security

- All log entries include the user ID of who made the change
- Logs are read-only once created
- Access to logs should be restricted based on user permissions

## Performance Considerations

- Indexes are created on frequently queried columns
- Logs are stored with proper foreign key constraints
- Consider archiving old logs for better performance

## Example Queries

### Get all grade level changes for a student
```php
$logs = EnrollmentLog::where('student_id', $studentId)
    ->where('action_type', 'grade_level_change')
    ->orderBy('changed_at', 'desc')
    ->get();
```

### Get all changes made by a specific user
```php
$logs = EnrollmentLog::where('user_id', $userId)
    ->with(['enrollment.student', 'enrollment.gradeLevel'])
    ->orderBy('changed_at', 'desc')
    ->get();
```

### Get recent changes across all enrollments
```php
$logs = EnrollmentLog::with(['enrollment.student', 'user'])
    ->where('changed_at', '>=', now()->subDays(30))
    ->orderBy('changed_at', 'desc')
    ->get();
```



