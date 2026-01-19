# CCMScanner

A PHP class library for retrieving CCM data lists. It fetches data in JSON format from a remote API and performs filtering based on specified conditions.

- Version 1.00  [20th Jan. 2026]

## Requirements

- PHP 8.1 or higher

## Installation

Place `CCMScanner.php` in your project directory.  
For example, under `/usr/local/lib/php` or similar location.

```bash
git clone git@github.com:mhorimoto/ccmscanner.git
cd ccmscanner
```

## Basic Usage

### 1. Load the Class

```php
require_once('CCMScanner.php');
```

### 2. Create an Instance

```php
// Use the default API endpoint
$scanner = new CCMScanner();

// Or specify a custom API endpoint
$scanner = new CCMScanner('https://your-api-endpoint.com/api/data.php');
```

### 3. Retrieve Data

```php
// Retrieve all data
$allData = $scanner->scan();

// Filter by specific conditions
$filteredData = $scanner->scan(['ROOM' => 45]);

// Filter by multiple conditions
$filteredData = $scanner->scan([
    'ROOM' => 45,
    'CCMTYPE' => 'temperature'
]);
```

### 4. Use the Retrieved Data

```php
foreach ($filteredData as $item) {
    echo "ROOM: {$item['ROOM']}, VALUE: {$item['VALUE']}\n";
}
```

## Sample Code

See `ccmscan_test.php` for a complete working example.

```php
#!/usr/bin/php
<?php
require_once('CCMScanner.php');
$scanner = new CCMScanner();

// Retrieve data for ROOM number 45
$data = $scanner->scan(['ROOM' => 45]);

// Display results
foreach ($data as $n => $item) {
    $dateStr = date('Y-m-d H:i:s', $item['FTIME']); 
    echo "[$n] {$item['ROOM']}-{$item['REGION']}-{$item['ORDER']}-{$item['PRIORITY']}/{$item['CCMTYPE']}/{$item['VALUE']}/ {$dateStr}\n";
}
?>
```

### Example Output

```
[0] 45-1-1-15/WAirHumid/93.61/ 2026-01-20 00:11:20
[1] 45-1-1-15/WAirTemp/6.48/ 2026-01-20 00:11:20
[2] 45-1-1-15/WRadation/0/ 2026-01-20 00:11:20
[3] 45-1-1-15/WRadition/0/ 2026-01-20 00:11:20
[4] 45-1-1-29/cnd/0/ 2026-01-20 00:11:29
[5] 45-114-10-15/FLOW.mNB/0.0/ 2026-01-20 00:11:03
[6] 45-114-10-29/cnd.mNB/0/ 2026-01-20 00:11:31
[7] 45-114-10-5/CPUTEMP.mNB/23725/ 2026-01-20 00:11:03
[8] 45-2-1-15/EC_BULK/0.63/ 2026-01-20 00:10:47
[9] 45-2-1-15/EC_PORE/2.59/ 2026-01-20 00:10:47
[10] 45-2-1-15/InAirCO2/788/ 2026-01-20 00:11:24
[11] 45-2-1-15/InAirHumid/98.95/ 2026-01-20 00:11:13
[12] 45-2-1-15/InAirTemp/13.54/ 2026-01-20 00:11:13
[13] 45-2-1-15/PPFD/0/ 2026-01-20 00:10:47
```
The above example shows a limited number of results due to the ROOM=45 filter, but in practice, the data can reach several hundred items.

```

### How to Run

```bash
chmod +x ccmscan_test.php
./ccmscan_test.php
```

or

```bash
php ccmscan_test.php
```

## Data Structure

The data retrieved from the API has the following structure:

```php
[
    [
        'ROOM' => 45,
        'REGION' => 1,
        'ORDER' => 1,
        'PRIORITY' => 15,
        'CCMTYPE' => 'WAirHumid',
        'VALUE' => 93.61,
        'FTIME' => 1737331880  // Unix timestamp
    ],
    [
        'ROOM' => 45,
        'REGION' => 2,
        'ORDER' => 1,
        'PRIORITY' => 15,
        'CCMTYPE' => 'InAirTemp',
        'VALUE' => 13.54,
        'FTIME' => 1737331873
    ],
    // ...
]
```

### Common CCMTYPEs (Sensor Types)

Main sensor types retrieved from actual data:

- `WAirHumid`: Outdoor air humidity
- `WAirTemp`: Outdoor air temperature
- `WRadation`, `WRadition`: Radiation
- `InAirHumid`: Indoor air humidity
- `InAirTemp`: Indoor air temperature
- `InAirCO2`: CO2 concentration
- `EC_BULK`, `EC_PORE`: Electrical conductivity
- `PPFD`: Photosynthetic Photon Flux Density
- `FLOW.mNB`: Flow rate
- `cnd`, `cnd.mNB`: Conductivity
- `CPUTEMP.mNB`: CPU temperature

**This library can retrieve all CCMs received by the UECS server.**

## Methods

### `__construct($apiUrl = 'https://foo.bar.com/api/hogehoge.php')`

Constructor. Specifies the API endpoint URL.

**Parameters:**
- `$apiUrl` (string, optional): API endpoint URL

### `scan($filters = [])`

Retrieves data from the remote API and returns it filtered by the specified conditions.

**Parameters:**
- `$filters` (array, optional): Associative array of filtering conditions

**Return value:**
- `array`: Array of filtered data. Returns an empty array on error.

**Examples:**

```php
// No filter
$allData = $scanner->scan();

// Single condition
$data = $scanner->scan(['ROOM' => 45]);

// Multiple conditions (AND condition)
$data = $scanner->scan([
    'ROOM' => 45,
    'REGION' => 1
]);
```

## Error Handling

- Returns an empty array `[]` if the API connection fails or JSON parsing fails.
- Timeout is set to 5 seconds.

## Notes

- This library disables SSL certificate verification (`verify_peer` and `verify_peer_name` are set to `false`). It is recommended to implement proper certificate verification in production environments.
- Access to the API endpoint may require appropriate authentication and authorization.

## License

MIT License

## Author

Masafumi Horimoto (<mh@ys-lab.tech>)

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.