<!DOCTYPE html>
<html>
<head>
    <title>Formulari</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            color: #333333;
            margin-bottom: 5px;
        }
        .form-control, .form-control-file {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #dddddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-control-file {
            padding: 5px;
        }
        .form-control:focus, .form-control-file:focus {
            border-color: #5cb3fd;
            outline: none;
        }
        button {
            width: 100%;
            padding: 10px 15px;
            font-size: 16px;
            background-color: #5cb3fd;
            border: none;
            border-radius: 4px;
            color: #ffffff;
            cursor: pointer;
        }
        button:hover {
            background-color: #4a9edc;
        }
    </style>

</head>
<body>
    <div class="container">
        <h1>New Item</h1>
        <form action="{{ route('form.submit') }}" method="POST" enctype="multipart/form-data">
            
            @csrf
            <!-- Títol -->
            <div class="form-group">
                <label for="title">Títol:</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>

            <!-- Descripció -->
            <div class="form-group">
                <label for="description">Descripció:</label>
                <textarea id="description" name="description" class="form-control" required></textarea>
            </div>

            <!-- Instal·lació -->
            <div class="form-group">
                <label for="installation">Instal·lació:</label>
                <textarea id="installation" name="installation" class="form-control" required></textarea>
            </div>

            <!-- URL de la Imatge -->
            <div class="form-group">
                <label for="image">Imatge:</label>
                <input type="file" id="image" name="image" class="form-control-file" accept="image/*" required>
            </div>

            <!-- URL de l'Arxiu -->
            <div class="form-group">
                <label for="file">Arxiu:</label>
                <input type="file" id="file" name="file" class="form-control-file" accept=".pdf,.doc,.docx,.txt" required>
            </div>

            <!-- Web -->
            <div class="form-group">
                <label for="web">Web:</label>
                <input type="url" id="web" name="web" class="form-control">
            </div>

            <!-- GitHub -->
            <div class="form-group">
                <label for="github">GitHub:</label>
                <input type="url" id="github" name="github" class="form-control">
            </div>

            <!-- Categories -->
            <div class="form-group">
                <label for="categories">Categories:</label>
                <select id="categories" name="categories" class="form-control" required>
                    <option value="Jocs">Jocs</option>
                    <option value="Dev">Dev</option>
                    <option value="Extensions">Extensions</option>
                   
                </select>
            </div>

            <!-- Tipus -->
            <div class="form-group">
                <label for="type">Tipus:</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="WASM">WASM</option>
                    <option value="WASI">WASI</option>
                    <option value="Projecte">Projecte</option>
                    
                </select>
            </div>

            <!-- Botó d'enviar -->
            <button type="submit" class="btn btn-primary">Crear</button>
        </form>
    </div>


</body>
</html>
