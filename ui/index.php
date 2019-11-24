<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Soap Client Demp</title>
    <link href="<?php echo web_base_path('ui/assets/bootstrap.min.css'); ?>" rel="stylesheet">
    <script src="<?php echo web_base_path('ui/assets/vue.js'); ?>"></script>
</head>
<body>

<div class="container">
    <div id="app">
        <div class="panel panel-default">
            <div class="panel-heading"></div>
            <div class="panel-body">
                <div class="table-responsive">
                    <form action="client.php" method="post">
                        <div class="form-group text-center">
                            <button class="btn btn-sm btn-danger" type="submit" name="reset" value="1">
                                Reset
                            </button>
                        </div>

                        <table class="table table-hover table-condensed table-striped">
                            <thead>
                            <th></th>
                            <th>Nume</th>
                            <th>Prenume</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            </thead>
                            <tbody>
                            <tr v-for="row of table">
                                <td v-for="(col, field) of row">
                                    <span v-if="editRow === row.id && field !== 'id'">
                                        <input type="text" :value="col" :name="field" class="form-control">
                                    </span>
                                    <span v-else>
                                       <span v-if="field === 'id'">#</span> {{ col }}
                                    </span>
                                </td>
                                <td>
                                    <div v-if="editRow === row.id">
                                        <button class="btn btn-xs btn-default" type="button" @click="cancelEdit()">
                                            Anulare
                                        </button>
                                        <button class="btn btn-xs btn-success" type="submit" name="edit_id"
                                                :value="row.id">
                                            Salvare
                                        </button>
                                    </div>
                                    <div v-else>
                                        <button class="btn btn-xs btn-warning" type="button" @click="makeEditable(row)">
                                            Edit
                                        </button>
                                        <button class="btn btn-xs btn-danger" type="submit" name="delete_id"
                                                :value="row.id">
                                            Sterge
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>

                    <form action="client.php" method="post" class="form-inline">
                        <div class="form-group">
                            <label for="first_name">Nume</label>
                            <input type="text" class="form-control" name="first_name" id="first_name">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Prenume</label>
                            <input type="text" class="form-control" name="last_name" id="last_name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" name="email" id="email">
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefon</label>
                            <input type="text" class="form-control" name="phone" id="phone">
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-sm btn-success" type="submit" name="add" value="1">
                                Adaugare
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
//
//var_dump($soapClient->getLastResponse());
//
//?>

<script>
    (function () {
        var userData = <?php print_json(convertSoapArrayCollection($soapClient->browseUsers())); ?>;

        new Vue({
            el: '#app',
            data: function () {
                return {
                    table: userData,
                    editRow: null
                };
            },
            methods: {
                makeEditable: function (row) {
                    this.editRow = row.id;
                },
                cancelEdit: function () {
                    this.editRow = null;
                }
            }
        });
    })();
</script>
</body>
</html>