# 🚀 QUICK START GUIDE

## Prerequisites
- .NET 9.0 SDK
- SQL Server LocalDB (included with Visual Studio)

## Steps to Run

### 1. Clone or open the project
```bash
cd restoran
```

### 2. Restore packages
```bash
dotnet restore
```

### 3. Update database (already done)
```bash
dotnet ef database update
```

### 4. Run the application
```bash
dotnet run
```

### 5. Open Swagger UI
Navigate to: **https://localhost:5001**

## Testing the API

### Option 1: Swagger UI
- Open https://localhost:5001 in browser
- Try out endpoints directly

### Option 2: API_Tests.http file
- Open API_Tests.http in VS Code
- Install REST Client extension
- Click Send Request above each request

### Option 3: Postman/Insomnia
- Import from Swagger: https://localhost:5001/swagger/v1/swagger.json

## Example Workflow

See API_REFERENCE.md for complete workflow example.
