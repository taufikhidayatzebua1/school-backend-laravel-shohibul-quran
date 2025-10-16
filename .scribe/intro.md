# Introduction

API for managing Hafalan Al-Quran system including students, classes, and memorization tracking.

<aside>
    <strong>Base URL</strong>: <code>http://localhost/api/v1</code>
</aside>

    This documentation provides comprehensive information about the Hafalan API.
    
    ## Authentication
    All protected endpoints require authentication using Bearer tokens. 
    Obtain a token by logging in through the `/api/v1/auth/login` endpoint.
    
    ## Rate Limiting
    - Public endpoints: 60 requests per minute
    - Auth endpoints: 10 requests per minute  
    - Protected endpoints: 200 requests per minute
    
    ## API Versioning
    All endpoints are prefixed with `/api/v1`.

