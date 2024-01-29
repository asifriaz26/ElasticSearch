<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elastic Search</title>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

</head>
<body>

<h1 style="color: #333; text-align:center" id="text">Search Form</h1>

<form action="/searchElastic" method="post" style="max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
    @csrf
    <label for="searchInput" style="display: block; margin-bottom: 10px; font-weight: bold; color: #555;">Search:</label>
    <input type="text" id="searchInput" name="searchText" style="width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px;" placeholder="Enter your search query">
    <select name="product_id" id="product" class="form-control select2" style="width:100% !important;">
        <option value=""> Select Product </option>
        <option value="aiyvien e">Testing</option>
    </select>
    <br><br>
    <button type="submit" style="background-color: #4caf50; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Search</button>

</form>
<script type="text/javascript">

    $(document).ready(function() {
        $('#product').select2({
            ajax: {
                url: "{{ route('searchElastic') }}",
                dataType: 'json',
                delay: 250,
                type: 'GET',
                contentType: 'application/json;charset=utf-8',
                data: function (params) {
                    return {
                        q: params.term // search term
                    };
                },
                processResults: function (data) {
                    var formattedData = data.map(function (result) {
                        return {
                            id: result._source.item_id,
                            text: result._source.item_title_en
                        };
                    });

                    return {
                        results: formattedData
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 1
        });
    });


</script>
</body>
</html>
