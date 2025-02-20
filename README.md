# PHP Scatter Plot Program

This PHP program loads data from a CSV file and generates a scatter plot using the `jpgraph` library.

## Requirements

- PHP (version 7.4 or later)
- `jpgraph` library

## Installation

1. Install the `jpgraph` library. You can download it from [jpgraph.net](http://jpgraph.net/download/).
2. Extract the `jpgraph` library to a directory accessible by your PHP server.
3. Place the `PHP Quiz Question #2 - out.csv` file in the same directory as the PHP script.

## Usage

1. Ensure that your server supports PHP and has the `jpgraph` library installed.
2. Place the CSV file and the PHP script in the same directory.
3. Run the PHP script on your server. The script will generate a scatter plot and display it.

```php name=plot_scatter.php
<?php
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_scatter.php');

// Load the CSV file
$data = array_map('str_getcsv', file('PHP Quiz Question #2 - out.csv'));
array_shift($data); // Remove the header row

// Extract x and y coordinates
$x_data = array();
$y_data = array();
foreach ($data as $row) {
    $x_data[] = (int)$row[0];
    $y_data[] = (int)$row[1];
}

// Create the graph
$graph = new Graph(800, 600);
$graph->SetScale('linlin');

// Create the scatter plot
$scatterplot = new ScatterPlot($y_data, $x_data);
$scatterplot->mark->SetType(MARK_FILLEDCIRCLE);
$scatterplot->mark->SetWidth(4);

// Add the plot to the graph
$graph->Add($scatterplot);

// Set titles
$graph->title->Set('Scatter Plot of CSV Data');
$graph->xaxis->title->Set('X');
$graph->yaxis->title->Set('Y');

// Display the graph
$graph->Stroke();
```

## Reviewing the Data

Upon reviewing the data, we can observe the following:

### Range of Values

- The x-values range from 147 to 837.
- The y-values range from -48 to -946.

### Distribution

- The data points are spread across the range of x-values, with no specific clustering around any particular x-value.
- The y-values are generally negative and spread out, with a dense distribution of y-values around -500 to -600.

### Trends

- There does not appear to be a clear linear trend between the x and y values.
- The data points are scattered without a distinct pattern, indicating that there might not be a strong correlation between the x and y values.

### Outliers

- Some points have y-values that deviate significantly from the main cluster of points, indicating potential outliers.
