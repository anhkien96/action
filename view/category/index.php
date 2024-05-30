<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lưu danh mục</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="pt-4 pb-4">
            <div class="form-group">
                <label for="categories-json-data">Danh sách danh mục (JSON)</label>
                <textarea class="form-control" id="categories-json-data" rows="10"></textarea>
            </div>
            <button type="submit" id="form-submit" class="btn btn-primary mb-2">Lưu danh mục</button>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#form-submit').on('click', function(event) {
                event.preventDefault();
                let json_string_data = $('#categories-json-data').val();
                let data = null;
                try {
                    data = JSON.parse(json_string_data);
                }
                catch (e) {
                    console.error(e);
                }
                if (data) {
                    $.ajax({
                        type: 'POST',
                        url: '/?control=category&action=create',
                        data: { categories: data },
                        success: function(response) {
                            console.log(response);
                        }
                    });
                }
            });
        })
    </script>
</body>
</html>