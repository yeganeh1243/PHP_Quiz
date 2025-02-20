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