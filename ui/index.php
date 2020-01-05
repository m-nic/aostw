<?php runJsPrg(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $title; ?></title>
    <link href="<?php echo web_base_path('ui/assets/bootstrap.min.css'); ?>" rel="stylesheet">
    <script src="<?php echo web_base_path('ui/assets/vue.js'); ?>"></script>
    <style>
        pre {
            max-height: 180px;
        }
    </style>
</head>
<body class="<?php echo kebabCase($title); ?>">
<div class="container-fluid">
    <div id="app">
        <div class="row">
            <div class="col-xs-12 col-sm-8 app-block">
                <div class="panel panel-default">
                    <div class="panel-heading"></div>
                    <div class="panel-body">
                        <form action="" method="post">
                            <div class="form-group text-right">
                                <button class="btn btn-sm btn-danger" type="submit"
                                        @click="confirm($event, 'reset')"
                                        name="reset" value="1">
                                    Reset
                                </button>
                            </div>

                            <div class="table-responsive" v-if="table.length">
                                <table class="table table-hover table-condensed table-striped">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Nume</th>
                                        <th>Prenume</th>
                                        <th>Email</th>
                                        <th>Telefon</th>
                                        <th style="width: 150px"></th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="table.length">
                                    <tr v-for="row of table">
                                        <td v-for="(col, field) of row">
                                            <span v-if="editRow === row.id && field !== 'id'">
                                                <input type="text" :value="col" :name="field" class="form-control">
                                            </span>
                                            <span v-else>
                                                <span v-if="field === 'id'">#</span> {{ col }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <div v-if="editRow === row.id">
                                                <button class="btn btn-xs btn-default" type="button"
                                                        @click="cancelEdit()">
                                                    Anulare
                                                </button>
                                                <button class="btn btn-xs btn-success" type="submit"
                                                        @click="confirm($event, 'edit_id')" name="edit_id"
                                                        :value="row.id">
                                                    Salvare
                                                </button>
                                            </div>
                                            <div v-else>
                                                <button class="btn btn-xs btn-warning" type="button"
                                                        @click="makeEditable(row)">
                                                    Edit
                                                </button>
                                                <button class="btn btn-xs btn-danger" type="submit"
                                                        @click="confirm($event, 'delete_id')" name="delete_id"
                                                        :value="row.id">
                                                    Sterge
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="alert alert-warning text-center">
                                Nu exista date! Puteti adauga sau apasa pe "Reset"
                            </div>
                        </form>

                        <div class="row">
                            <div class="col-xs-12 col-sm-4 col-sm-offset-4 well well-lg">
                                <form action="" method="post"
                                      class="form-horizontal">
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
            <div class="col-xs-12 col-sm-4 requests-block">
                <div class="panel switcher">
                    <div class="panel-body text-right">
                        <a href="/client-soap.php"
                           class="btn <?php echo $_SERVER['SCRIPT_NAME'] === '/client-soap.php' ? 'btn-primary' : 'btn-default'; ?>">
                            SOAP
                        </a>
                        <a href="/client-rest.php"
                           class="btn <?php echo $_SERVER['SCRIPT_NAME'] === '/client-rest.php' ? 'btn-primary' : 'btn-default'; ?>">
                            REST
                        </a>
                    </div>
                </div>

                <div class="panel panel-primary" v-for="req of requestsStack">
                    <div class="panel-heading">{{ req.method }}</div>
                    <div class="panel-body">

                        <div class="panel panel-info">
                            <div class="panel-heading">Request</div>
                            <div class="panel-body">
                                <pre>{{ req.request.headers }}</pre>
                            </div>
                            <div class="panel-body">
                                <pre>{{ req.request.body }}</pre>
                            </div>
                        </div>

                        <div class="panel panel-success">
                            <div class="panel-heading">Response</div>
                            <div class="panel-body">
                                <pre>{{ req.response.headers }}</pre>
                            </div>
                            <div class="panel-body">
                                <pre>{{ req.response.body }}</pre>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var userData = <?php print_json($userData); ?>;
        var requestsStack = <?php print_json($requestsStack); ?>;

        new Vue({
            el: '#app',
            data: function () {
                return {
                    table: userData,
                    editRow: null,
                    requestsStack: requestsStack
                };
            },
            methods: {
                makeEditable: function (row) {
                    this.editRow = row.id;
                },
                cancelEdit: function () {
                    this.editRow = null;
                },
                confirm: function ($event) {
                    if (!confirm('Confirmati actiunea?')) {
                        $event.preventDefault();
                    }
                }
            }
        });
    })();
</script>
</body>
</html>