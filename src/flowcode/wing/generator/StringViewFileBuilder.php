<?php

namespace flowcode\wing\generator;

use flowcode\orm\domain\Mapper;

/**
 * Description of StringFileBuilder
 *
 * @author Juan Manuel Agüero <jaguero@flowcode.com.ar>
 */
class StringViewFileBuilder {

    public static function getDomainForm(Mapper $mapper, $module) {
        $stringFile = '<form class="form-horizontal form">
    <input type="hidden" name="id" value="<?php echo $viewData["' . $mapper->getName() . '"]->getId() ?>" />';
        foreach ($mapper->getPropertys() as $property) {
            if ($property->getColumn() != "id") {
                $stringFile .= '
    <div class="control-group">
        <label class="control-label" for="' . $property->getColumn() . '">' . $property->getName() . '</label>
        <div class="controls">
            <input type="text" name="' . $property->getColumn() . '" id="' . $property->getColumn() . '" value="<?php echo $viewData["' . $mapper->getName() . '"]->get' . $property->getName() . '() ?>"/>
        </div>
    </div>';
            }
        }
        foreach ($mapper->getRelations() as $relation) {
            $stringFile .= '
    <div class="control-group">
        <label class="control-label">' . $relation->getName() . '</label>
        <div class="controls">
            <select multiple="multiple" name="' . lcfirst($relation->getName()) . '[]" style="height: 137px;">
                <?php foreach ($viewData["' . lcfirst($relation->getName()) . '"] as $' . $mapper->getName() . '): ?>
                    <?php
                    $checked = "";
                    foreach ($viewData["' . $mapper->getName() . '"]->get' . $relation->getName() . '() as $post' . $relation->getName() . ') {
                        if ($' . $mapper->getName() . '->getId() == $post' . $relation->getName() . '->getId()) {
                            $checked = "selected";
                            break;
                        }
                    }
                    ?>
                    <option <?php echo $checked; ?> value="<?php echo $' . $mapper->getName() . '->getId(); ?>" ><?php echo $' . $mapper->getName() . '; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>';
        }
        $stringFile .= '

</form>

';
        return $stringFile;
    }

    public static function getDomainList(Mapper $mapper, $module) {
        $stringFile = '<script type="text/javascript">
    function actualizarPagina(valor) {
        $("#pagina-sel").val(valor);
        actualizarLista();
    }
    function actualizarLista() {
        var paginaSel = $("#pagina-sel").val();
        var searchSel = $("#search").val();

        var url = "#!admin' . ucfirst($mapper->getName()) . '/index";
        if (paginaSel) {
            url += "/page/" + paginaSel.toLowerCase();
        }
        if (searchSel) {
            url += "/search/" + encodeURI(searchSel);
        }
        window.location = url;
    }
    $(document).ready(function() {
        /* search form */
        $("#search").focus(function() {
            $(this).keyup(function(e) {
                if (e.keyCode === 13) {
                    actualizarLista();
                }
            });
        });
        $("#search").focus()
        
        /* create */
        $(".page-header > h1 > a").click(function() {
            createEntity("New ' . ucfirst($mapper->getName()) . '", $(this).attr("data-form"), $(this).attr("data-form-action"));
        });

        /* update */
        $("a.edit").click(function() {
            updateEntity("Edit ' . ucfirst($mapper->getName()) . '", "/admin' . ucfirst($mapper->getName()) . '/edit/" + $(this).attr("data-form-entity"), "/admin' . ucfirst($mapper->getName()) . '/save");
        });

        /* delete */
        $("a.delete").click(function() {
            if (confirm("Estás seguro?")) {
                deleteEntity("admin' . ucfirst($mapper->getName()) . '/delete/" + $(this).attr("data-form-entity"));
            }
        });
    });
</script>
<div class="page-header">
    <h1>' . ucfirst($mapper->getName()) . ' List
        <a class="btn" data-form="/admin' . ucfirst($mapper->getName()) . '/create" data-form-action="/admin' . ucfirst($mapper->getName()) . '/save" ><i class="icon-plus icon-white"></i> Nuevo</a>
    </h1>
</div>

<div class="form-search">
    <div class="input-append">
        <input id="search" type="text" name="search" placeholder="Buscar…" class="span8 search-query" value="<?php echo $viewData["filter"] ?>">
        <button type="button" class="btn" onclick="actualizarLista()" ><i class="icon-search icon-white"></i> Buscar</button>
    </div>
</div>

<table class="table">
    <thead>
        <tr>';
        foreach ($mapper->getPropertys() as $property) {
            $stringFile .= '
            <th>' . $property->getName() . '</th>';
        }
        $stringFile .= '            
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($viewData["pager"]->getItems() as $entity): ?>
            <tr>';
        foreach ($mapper->getPropertys() as $property) {
            $stringFile .= '
                <td><?php echo $entity->get' . $property->getName() . '() ?></td>';
        }
        $stringFile .= '            
                <td style="width: 100px;">
                    <a data-form-entity="<? echo $entity->getId() ?>" class="btn btn-mini edit" ><i class="icon-edit icon-white"></i></a>
                    <a data-form-entity="<? echo $entity->getId() ?>" class="btn btn-mini btn-danger delete"><i class="icon-remove icon-white"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p class="pull-right">
    Total de <?php echo $viewData["pager"]->getItemCount() ?> ' . $mapper->getName() . 's.
</p>
<input type="hidden" id="pagina-sel" value="" />
<ul class="pager">
    <li><a class="previous" onclick="actualizarPagina(<?php echo $viewData["pager"]->getPrevPage() ?>)"><i class="icon-chevron-left icon-white"></i></a></li>
    <span>pagina</span>
    <strong><?php echo $viewData["page"] ?></strong>
    <span>de <?php echo $viewData["pager"]->getPageCount() ?></span>
    <li><a class="next" onclick="actualizarPagina(<?php echo $viewData["pager"]->getNextPage() ?>)"><i class="icon-chevron-right icon-white"></i></a></li>
</ul>

';
        return $stringFile;
    }

}

?>
