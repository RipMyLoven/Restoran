# Restaurant Management System API

## Description
RESTful API for restaurant management system with order tracking, menu management, and statistics.

## Quick Start

1. Restore packages: `dotnet restore`
2. Update database: `dotnet ef database update`
3. Run application: `dotnet run`
4. Open Swagger UI: https://localhost:5001

## Features

- Restaurant management (CRUD)
- Table management
- Menu items with allergens and dietary info
- Order workflow (New -> Kitchen -> Ready -> Served -> Completed)
- Bill generation and payment
- Statistics and archived orders
- Full Swagger/OpenAPI documentation

## API Documentation

See API_REFERENCE.md for detailed endpoint documentation.
See API_Tests.http for example requests.

## Technologies

- ASP.NET Core 9.0
- Entity Framework Core 9.0
- SQL Server LocalDB
- Swagger/OpenAPI
