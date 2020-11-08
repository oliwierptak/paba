# PABA

PABA - PHP Apache Benchmark Analyser: analyses output of ab (Apache Benchmark) and generates CSV file


## Installation

```sh
composer require paba/paba
```

## Usage

Define a config file, and output csv path, for example:

```sh
bin/paba generate -c tests/fixtures/example.ini -o /tmp/example.csv
```

### Ini example
```ini
[site::google]
url = "/?"
comment = "Lorem Ipsum"
run = 10
concurrency = 1
step = 1
repeat = 10
timeout = 120
host = https://google.com
sleep = 2
headers[foo] = bar

[site::wikipedia]
url = "/?"
comment = "Lorem Ipsum"
run = 10
concurrency = 1
step = 1
repeat = 10
timeout = 120
host = https://www.wikipedia.org
sleep = 0

```


### CSV Output

```csv
"Scenario Name",Runs,Repeat,"Document Path","Document Length (bytes)","Concurrency Level","Time taken for tests (seconds)","Complete requests","Failed requests","Total transferred (bytes)","HTML transferred (bytes)","Requests per second (sec)","Time per request (ms)","Transfer rate (Kbytes/sec)"
example::google,10,10,/?,220,1,1.580,10,0,7350,2200,6.33,158.008,4.54
example::google,10,10,/?,220,2,1.005,10,0,7350,2200,9.95,100.490,7.14
example::google,10,10,/?,220,3,1.037,10,0,7350,2200,9.64,103.703,6.92
example::google,10,10,/?,220,4,0.688,10,0,7350,2200,14.53,68.811,10.43
example::google,10,10,/?,220,5,0.503,10,0,7350,2200,19.87,50.338,14.26
example::google,10,10,/?,220,6,0.524,10,0,7350,2200,19.09,52.379,13.70
example::google,10,10,/?,220,7,0.520,10,0,7350,2200,19.24,51.981,13.81
example::google,10,10,/?,220,8,0.486,10,0,7350,2200,20.56,48.628,14.76
example::google,10,10,/?,220,9,0.398,10,0,7350,2200,25.15,39.767,18.05
example::google,10,10,/?,220,10,0.366,10,0,7350,2200,27.32,36.602,19.61
example::wikipedia,10,10,/?,68882,1,1.895,10,0,700450,688820,5.28,189.545,360.88
example::wikipedia,10,10,/?,68882,2,1.100,10,0,700450,688820,9.09,110.020,621.73
example::wikipedia,10,10,/?,68882,3,1.119,10,0,700450,688820,8.93,111.939,611.08
example::wikipedia,10,10,/?,68882,4,0.753,10,0,700450,688820,13.28,75.287,908.57
example::wikipedia,10,10,/?,68882,5,0.570,10,0,700450,688820,17.54,56.999,1200.08
example::wikipedia,10,10,/?,68882,6,0.576,10,0,700450,688820,17.37,57.571,1188.16
example::wikipedia,10,10,/?,68882,7,0.545,10,0,700450,688820,18.33,54.541,1254.15
example::wikipedia,10,10,/?,68882,8,0.535,10,0,700450,688820,18.69,53.497,1278.64
example::wikipedia,10,10,/?,68882,9,0.423,10,0,700450,688820,23.62,42.342,1615.49
example::wikipedia,10,10,/?,68882,10,0.357,10,0,700450,688820,27.98,35.735,1914.16
```
