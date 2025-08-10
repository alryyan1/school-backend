# StudentFactory Documentation

## Overview

The `StudentFactory` has been refactored to work with the new `wished_school` field instead of the deprecated `wished_level` field. This factory provides comprehensive methods for creating test data for students.

## Basic Usage

### Creating a Basic Student

```php
use App\Models\Student;

// Create a single student with random data
$student = Student::factory()->create();

// Create multiple students
$students = Student::factory(10)->create();
```

## Factory Methods

### Approval Status Methods

#### `approved()`
Creates a student with approved status.

```php
$student = Student::factory()->approved()->create();
// Result: approved = true, aproove_date = now(), approved_by_user = admin user ID
```

#### `pending()`
Creates a student with pending approval status.

```php
$student = Student::factory()->pending()->create();
// Result: approved = false, aproove_date = null, approved_by_user = null
```

### School Assignment Methods

#### `withWishedSchool(School $school)`
Assigns a specific school as the wished school.

```php
use App\Models\School;

$school = School::first();
$student = Student::factory()->withWishedSchool($school)->create();
// Result: wished_school = $school->id
```

#### `withoutWishedSchool()`
Creates a student with no wished school assigned.

```php
$student = Student::factory()->withoutWishedSchool()->create();
// Result: wished_school = null
```

### Gender Methods

#### `male()`
Creates a male student.

```php
$student = Student::factory()->male()->create();
// Result: gender = 'ذكر'
```

#### `female()`
Creates a female student.

```php
$student = Student::factory()->female()->create();
// Result: gender = 'انثي'
```

## Method Chaining

You can combine multiple methods to create students with specific characteristics:

```php
// Create an approved male student with a specific wished school
$student = Student::factory()
    ->approved()
    ->male()
    ->withWishedSchool($school)
    ->create();

// Create a pending female student without a wished school
$student = Student::factory()
    ->pending()
    ->female()
    ->withoutWishedSchool()
    ->create();
```

## Default Data Generated

The factory generates realistic Arabic names and data:

### Personal Information
- **student_name**: Full Arabic name (4-part name)
- **email**: Optional email (70% chance)
- **date_of_birth**: Between 5-18 years ago
- **gender**: 'ذكر' or 'انثي'
- **goverment_id**: Optional national ID (80% chance)
- **medical_condition**: Optional medical condition (5% chance)

### Parent Information
- **father_name**: Arabic father name
- **father_job**: Common Arabic job titles
- **father_address**: Arabic-style address
- **father_phone**: Saudi mobile format (05########)
- **father_whatsapp**: Optional WhatsApp number (60% chance)

- **mother_name**: Arabic mother name
- **mother_job**: Common Arabic job titles
- **mother_address**: Arabic-style address
- **mother_phone**: Saudi mobile format
- **mother_whatsapp**: Optional WhatsApp number (60% chance)

### Other Guardian Information
- **other_parent**: Optional other guardian (10% chance)
- **relation_of_other_parent**: Relationship type
- **relation_job**: Job title
- **relation_phone**: Phone number
- **relation_whatsapp**: Optional WhatsApp number

### Approval Information
- **approved**: Boolean (default: 85% chance of true)
- **aproove_date**: Timestamp if approved
- **approved_by_user**: Admin user ID if approved
- **message_sent**: Boolean (60% chance if approved)

## Seeder Integration

The `StudentSeeder` has been updated to work with the new factory:

```php
// In DatabaseSeeder.php
$this->call([
    // ... other seeders
    StudentSeeder::class, // Creates 50 students (70% approved, 30% pending)
]);
```

### StudentSeeder Features
- Creates 50 students by default
- 70% approved, 30% pending
- Automatically assigns wished schools from existing schools
- Provides informative output

## Testing

Use the provided test command to verify factory functionality:

```bash
php artisan test:student-factory
```

This command tests all factory methods and provides detailed output.

## Migration from wished_level

### Before (Deprecated)
```php
'wished_level' => $this->faker->randomElement(['روضه', 'ابتدائي', 'متوسط', 'ثانوي'])
```

### After (Current)
```php
'wished_school' => School::inRandomOrder()->first()?->id ?? null
```

## Dependencies

The factory requires:
- `School` model (for wished_school assignment)
- `User` model (for approved_by_user assignment)
- Schools must be seeded before students

## Error Handling

The factory includes safety checks:
- Falls back to `null` if no schools exist
- Falls back to `null` if no admin users exist
- Handles optional fields gracefully

## Best Practices

1. **Always seed schools first** before creating students
2. **Use specific methods** for testing different scenarios
3. **Chain methods** for complex test data
4. **Test with the provided command** to verify functionality
5. **Use realistic data** for better testing scenarios 