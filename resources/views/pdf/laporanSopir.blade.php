<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        table, th, td {
          border:1px solid black;
        }

        h1, h2 {
            text-align: center;
        }
        </style>
</head>
  <body>
    <h1>{{$title}}</h1>
    <h2>{{$date}}</h2>

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Date</th>
            <th>Driver Name</th>
            <th>Vehicle Registration Number</th>
            <th>Delivery Status</th>
        </tr>
        @php $i=1; @endphp
        @foreach ($drivers as $driver)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$driver->tgl}}</td>
                <td>{{$driver->nama_sopir}}</td>
                <td>{{$driver->nopol}}</td>
                <td>{{$driver->sts}}</td>
            </tr>
        @endforeach
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>

