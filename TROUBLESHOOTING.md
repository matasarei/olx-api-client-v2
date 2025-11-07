# Troubleshooting

The aim of this section is to familiarise you with the possible errors that
may appear during the development process or directly after launching the
integration for the very first time.

## Error handling

This API uses standard HTTP status codes to indicate the status of a
response.

|Name |Code|Description |
|---|---|---|
|Bad request |400 |The request was unacceptable. For example validation didn’t pass. |
|Unauthorized |401 |The request has not been applied because it lacks valid authentication credentials for the target resource. |
|Forbidden |403 |The server understood the request, but is refusing to fulfill it |
|Not Found |404 |The server has not found anything matching the request URI |
|Not acceptable |406 |The server is unable to return a response in the format that was requested by the client |
|Unsupported Media Type |415 |The server is refusing to service the request because the entity of the request is in a format not supported by the requested resource for the requested method|
|Too many requests |429 |Too many requests hit the API too quickly |
|Server error |500 |An error that is not caused by client, something bad happend on server |

**Example**

```
{
  "error": {
    "status": 400,
    "title": "Invalid request",
    "detail": "Data validation error occurred",
    "validation": [
    {
      "field": "title",
      "title": "Musisz podać tytuł",
      "detail": "Musisz podać tytuł"
    },
    {
      "field": "description",
      "title": "Musisz podać opis",
      "detail": "Musisz podać opis"
    }
    ]
  }
}
```

## Authorization errors

These errors are related to the authorization process. Below you can find
the most common situations and proposed solutions.

### 1. Missing parameter: `code` is required

**HTTP code: `400 Bad Request`**

```
{
  "error": "invalid_request",
  "error_description": "Missing parameter: \"code\" is required",
}
```

**Solution**:

* provide missing `code` parameter in the request

**Example:**

```
{
  "grant_type":"authorization_code"
  "scope":"read write v2"
  "client_id":"<your client_id>"
  "client_secret":"<your client_secret>"
  "code":"<one time authorization token>"
}
```

### 2. Authorization code doesn't exist or is invalid for the client

**HTTP code: `400 Bad Request`**

```
{
  "error": "invalid_grant",
  "error_description": "Authorization code doesn't exist or is invalid for the client",
}
```

**Solution**:

* provide valid `code` parameter in the request (please remember that it is
valid for 10 minutes only)

### 3. The grant type was not specified in the request

**HTTP code: `400 Bad Request`**

```
{
  "error": "invalid_request",
  "error_description": "The grant type was not specified in the request",
}
```

**Solution**:

* provide required `grant_type` parameter in the request

### 4. The scope requested is invalid for this client

**HTTP code: `400 Bad Request`**

```
{
  "error": "invalid_scope",
  "error_description": "The scope requested is invalid for this client",
}
```

**Solution**:

* verify provided `scope`
* make sure that your API account is allowed to use a given `scope`

### 5. Client is not active

**HTTP code: `401 Unauthorized`**

```
{
  "error": "invalid_client",
  "error_description": "Client is not active",
}
```

**Solution**:

* verify `client_id` provided in the request

### 6. The client credentials are invalid

**HTTP code: `400 Bad Request`**

```
{
  "error": "invalid_client",
  "error_description": "The client credentials are invalid",
}
```

**Solution**:

* verify `client_id` and `client_secret` provided in the request

### 7. The access token provided is invalid

**HTTP code: `401 Unauthorized`**

```
{
  "error": "invalid_token",
  "error_description": "The access token provided is invalid",
}
```

**Solution**:

* verify `access_token` provided in the request

### 8. Insufficient scope

**HTTP code: `401 Unauthorized`**

```
{
    "error": "insufficient_scope",
    "error_description": "The request requires higher privileges than provided by the access token",
    "error_human_title": "Insufficient scope."
}
```

**Solution**:

* you need to authorize yourself with a higher privileges
* make sure that you are authorized with a proper scopes: `read write v2`
instead of `read write`

### 9. Invalid owner in token

```
{
  "error": {
    "status": 400,
    "title": "Bad Request",
    "detail": "Invalid owner in token"
  }
}
```

**Solution**:

* make sure that you are authenticated in the user context -
`"grant_type":"authorization_code"` instead of
`"grant_type":"client_credentials"`

### 10. The grant type is unauthorized for this client_id

```
{
    "error": "unauthorized_client",
    "error_description": "The grant type is unauthorized for this client_id",
    "error_human_title": "Unauthorized client."
}
```

**Solution**:

* your API account is not allowed to use a given `grant_type`
* reach out to us in order to check it out

### 11. Invalid refresh token

```
{
    "error": "invalid_grant",
    "error_description": "Invalid refresh token",
    "error_human_title": "Provided authorization credentials are invalid or expired."
}
```

**Solution**:

* make sure that `refresh_token` is valid (please remember that it lasts for
2592000 seconds and can be changed when new access token is generated)
* authenticate yourself once again in order to receive a new one

## Publishing errors

These errors happen when something goes wrong during the advert publishing.

### 1. Category with given ID doesn't exists

```
{
  "error": {
    "status": 400,
    "title": "Bad Request",
    "detail": "Category with given ID doesn't exists"
  }
}
```

**Solution**:

* provide valid `category_id` in the request

### 2. Fix the category

```
{
  "error": {
    "status": 400,
    "title": "Invalid request",
    "detail": "Data validation error occurred",
    "validation": [
      {
        "field": "category_id",
        "title": "Fix the category",
        "detail": "Fix the category"
      }
    ]
  }
}
```

**Solution**:

* provide valid `category_id` in the request
* make sure that the `category_id` is a leaf category:

```
GET /api/partner/categories/1755

{
  "data": {
      "id": 1755,
      "name": "Praca inżynieryjna, techniczna",
      "parent_id": 1447,
      "photos_limit": 0,
      "is_leaf": true
  }
}
```

### 3. Error while decoding JSON data: Syntax error

```
{
  "data": {
    "error": {
      "status": 400,
      "title": "Bad Request",
      "detail": "Error while decoding JSON data: Syntax error"
    }
  }
}
```

**Solution**:

* make sure that the payload you are sending does not contain any extra
fields
* make sure that the field names are valid and do not contain any typos

### 4. Partner is not allowed to use external URL

```
{
  "error": {
    "status": 400,
    "title": "Bad Request",
    "detail": "Partner is not allowed to use external URL"
  }
}
```

**Solution**:

* your API account is not allowed to use `external_url` field
* this feature is available only for a Jobs category in PL
* reach out to us in order to enable this option

### 5. Invalid value: `district_id`

```
{
  "error": {
    "status": 400,
    "title": "Invalid request",
    "detail": "Data validation error occurred",
    "validation": [
      {
        "field": "district_id",
        "title": "Invalid value"
      }
    ]
  }
}
```

**Solution**:

* verify `district_id` provided in the request:

```
GET /api/partner/cities/{cityId}/districts
```

### 6. Your coordinates are too far from picked location

```
{
  "error": {
    "status": 400,
    "title": "Invalid request",
    "detail": "Data validation error occurred",
    "validation": [
      {
        "field": "district/city_id",
        "title": "Your coordinates are too far from picked location."
      }
    ]
  }
}
```

**Solution**:

* verify if `latitude` and `longitude` fields are valid for a given
district/city_id:

```
GET /api/partner/locations/?latitude={lat}&longitude={lon}
```

### 7. This value is not valid: `attributes`

```
{
  "error": {
    "status": 400,
    "code": 400,
    "title": "Invalid request",
    "detail": "Data validation error occurred",
    "validation": [
      {
        "field": "attributes",
        "title": "This value is not valid."
      }
    ]
  }
}
```

**Solution**:

* make sure that the `attributes` array contains `code` and `value` keys
only:

```
"attributes": [
  {
    "code": "type",
    "value": "fulltime"
  },
  {
    "code": "contract",
    "value": "contract"
  },
  {
    "code": "manager",
    "value": "0"
  },
  {
    "code": "remote_recruitment",
    "value": "0"
  }
]
```

### 8. Invalid value: `params.state`

```
{
  "error": {
    "status": 400,
    "title": "Invalid request",
    "detail": "Data validation error occurred",
    "validation": [
      {
        "field": "params.state",
        "title": "Invalid value"
      }
    ]
  }
}
```

**Solution**:

* verify `state` value in the `attributes` array

### 9. Image error: Remote file not exists

```
{
  "error": {
    "status": 400,
    "title": "Bad Request",
    "detail": "Image error: Remote file not exists"
  }
}
```

**Solution**:

* verify all URLs provided in the `images` array

### 10. Image error: Image limit exceeded

```
{
  "error": {
    "status": 400,
    "title": "Bad Request",
    "detail": "Image error: Image limit exceeded"
  }
}
```

**Solution**:

* make sure that the request does not contain more images than it is
allowed:

```
GET /api/partner/categories/1581

{
  "data": {
      "id": 1581,
      "name": "Bluzki i koszulki",
      "parent_id": 642,
      "photos_limit": 8,
      "is_leaf": true
  }
}
```

### 11. Unsupported API version

**HTTP code: `404 Not Found`**

```
{
  "error": {
    "type": "NotFoundException",
    "message": "Unsupported API version"
  }
}
```

**Solution**:

* make sure that the URL you are trying to call to is valid

### 12. Advert not found

```
{
  "error": {
    "status": 404,
    "title": "Not Found",
    "detail": "Advert not found"
  }
}
```

**Solution**:

* make sure that the advert ID is valid

### 13. You are not the owner of this ad

```
{
  "error": {
    "status": 400,
    "title": "Invalid request",
    "detail": "Data validation error occurred",
    "validation": [
      {
        "field": "ad",
        "title": "You are not the owner of this ad"
      }
    ]
  }
}
```

**Solution**:

* you are not allowed to manage the ads created by other users

### 14. Too many capital letters

```
{
    "error": {
        "status": 400,
        "title": "Invalid request",
        "detail": "Data validation error occurred",
        "validation": [
            {
                "field": "title",
                "title": "Too many capital letters"
            }
        ]
    }
}
```

**Solution**:

* make sure that the title or description does not contain more than 50%
text written in capital letters

### 15. Field is not valid. Emails and www addresses are not allowed

```
{
    "error": {
        "status": 400,
        "title": "Invalid request",
        "detail": "Data validation error occurred",
        "validation": [
            {
                "field": "title",
                "title": "Field is not valid. Emails and www addresses are not allowed"
            }
        ]
    }
}
```

**Solution**:

* make sure that the title or description does not contain any e-mail/www
address

### 16. Field is not valid. Phone numbers are not allowed

```
{
    "error": {
        "status": 400,
        "title": "Invalid request",
        "detail": "Data validation error occurred",
        "validation": [
            {
                "field": "title",
                "title": "Field is not valid. Phone numbers are not allowed"
            }
        ]
    }
}
```

**Solution**:

* make sure that the title or description does not contain any phone number

### 17. Field contains to much punctuation

```
{
    "error": {
        "status": 400,
        "title": "Invalid request",
        "detail": "Data validation error occurred",
        "validation": [
            {
                "field": "title",
                "title": "Field contains to much punctuation"
            }
        ]
    }
}
```

**Solution**:

* following characters cannot be provided in the title and in the
description three times in a row: `!` `?` `.` `,` `-` `=` `+` `#` `%` `&`
`@` `*` `_` `>` `<` `:` `(` `)` `|`

### 18. Deactivate advert - ad has to be active

```
{
    "error": {
        "status": 400,
        "title": "Invalid request",
        "detail": "Data validation error occurred",
        "validation": [
            {
                "field": "ad",
                "title": "Ad has to be active"
            }
        ]
    }
}
```

**Solution**:

* make sure that the advert you want to deactivate is active

### 19. Delete advert - invalid status

```
{
    "error": {
        "status": 400,
        "title": "Invalid request",
        "detail": "Data validation error occurred",
        "validation": [
            {
                "field": "ad",
                "title": "Invalid status"
            }
        ]
    }
}
```

**Solution**:

* make sure that the advert you want to delete is not active
* deactivate the advert before deletion:

```
POST /api/partner/adverts/{advertId}/commands

{
  "command": "deactivate",
  "is_success": true // this flag indicates whether you have succeeded in selling the product or not
}
```

### 20. Cannot refresh advert

```
{
    "error": {
        "status": 400,
        "title": "Invalid request",
        "detail": "Data validation error occurred",
        "validation": [
            {
                "field": "time",
                "title": "You cannot refresh ad more often than once in 14 days"
            }
        ]
    }
}
```

**Solution**:

* depending on the country you cannot refresh advert more than once in a
given period of time
* please wait until the refresh option will be available for the advert

### 21. City with given ID doesn't exists

```
{
    "error": {
        "status": 400,
        "title": "Bad Request",
        "detail": "City with given ID doesn't exists"
    }
}
```

**Solution**:

* verify `city_id` provided in the request:

```
GET /api/partner/cities/{cityId}

{
    "error": {
        "status": 404,
        "title": "Not Found",
        "detail": "City not found"
    }
}
```

### 22. Invalid phone format

```
{
    "error": {
        "status": 400,
        "title": "Invalid request",
        "detail": "Data validation error occurred",
        "validation": [
            {
                "field": "phone",
                "title": "Invalid phone format"
            }
        ]
    }
}
```

**Solution**:

* make sure that the `phone` provided in the request is valid

### 23. Missing required 'Version' header

```
{
    "data": {
        "error": {
            "status": 400,
            "title": "Bad Request",
            "detail": "Missing required 'Version' header!"
        }
    }
}
```

**Solution**:

* add `"Version": "2.0"` header to your request

## Payment errors

Errors described in this section are related to the payment process

### 1. No possibility to buy the packet

```
{
    "error": {
        "status": 400,
        "title": "Bad Request",
        "detail": "There is no variant with size 123 for category 123"
    }
}
```

**Solution**:

* verify if the packet you are interested in is available for a given
category:

```
GET /api/partner/packets?category_id={categoryId}
```

### 2. Invalid payment method

```
{
    "error": {
        "status": 400,
        "title": "Bad Request",
        "detail": "Invalid payment method"
    }
}
```

**Solution**:

* make sure that the `payment_method` provided in the request is valid:

```
GET /api/partner/users/me/payment-methods
```

### 3. Payment method `postpaid` is not activated

```
{
    "error": {
        "status": 400,
        "title": "Bad Request",
        "detail": "Payment method 'postpaid' is not activated"
    }
}
```

**Solution**:

* `postpaid` method is not activated for the OLX account
* reach out to the local OLX support team to activate this option

### 4. Not enough credits

```
{
    "error": {
        "status": 400,
        "title": "Bad Request",
        "detail": "Not enough credits"
    }
}
```

**Solution**:

* make sure that there is enough credits on your OLX account to pay for a
given feature:

```
GET /api/partner/users/me/account-balance
```
