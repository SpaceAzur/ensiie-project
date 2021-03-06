<?php

use Db\Connection;
use Organization\Organization;
use Organization\OrganizationHydrator;
use Organization\OrganizationRepository;

$orgarepository = new OrganizationRepository(Connection::get(),new OrganizationHydrator());
$organizations = $orgarepository->fetchAll();

$hide = true;

?>

<div class="container-fluid" style="margin: 2em;">
    <div align="center" class="row" style="margin: 2em;">
        <div class="col">
            <label for="choix_organisations">Liste des organisations : </label>
            <select id="select-organizations" onchange="showformSelect()">
                <option></option>
                <?php /** @var Organization $organization */
                foreach ($organizations as $organization) { ?>
                    <option
                            data-name="<?php echo $organization->getName() ?>"
                            data-id=<?php echo $organization->getId() ?>
                    ><?php echo $organization->getName() ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col">
            <button id="add_orga" onclick="showform()">Ajouter une organisation</button>
        </div>
    </div>
    <div class="row" style="margin: 2em;">
        <div class="formulaire" id="formulaire" style="display: none">
            <input type="hidden" value="" name="id" id="id">
            <div class="container-fluid">
                <div class="form-row" align="center">
                    <legend>Organisation </legend>
                </div>
                <div class="form-row">
                    <label class="label-lenght-fix" for="username">Name : <em>*</em></label>
                    <input type="text"
                           value=""
                           name="name"
                           id="name"
                           required="">
                </div>
                <div class="form-row">
                    <span id="nameAlreadyExist" ></span>
                </div>
                <div class="form-row">
                    <span id="nameEmpty" ></span>
                </div>
                <div class="form-row">
                    <button id="button-addUpdate-Orga" type="submit"
                            onclick="addUpdateOrganization()" >Envoyer</button>
                    <button id="button-delete-Orga" type="submit"
                            onclick="DeleteOrganization()">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="div-select-bigBoss" align="center" style="display: none;">
        <div class="col" >
            <label for="select-futur-BigBoss">Choisir un utilisateur pour devenir BigBoss : </label>
            <select id="select-futur-BigBoss">
                <option></option>
                <?php
                include_once 'select_usersnotinorga.php';
                ?>
            </select>
        </div>
        <div class="col">
            <button id="button-futur-BigBoss" type="submit"
                    onclick="AddBigBossToOrga()">Add Big Boss</button>
        </div>
    </div>
</div>




<script>

    function AddBigBossToOrga() {
        var selectuser = $("#select-futur-BigBoss");
        var selectorga = $("#select-organizations");
        var index = selectuser.prop('selectedIndex');
        if(index>0){
            var iduser = selectuser.find(':selected').attr('data-id');
            var idorga = selectorga.find(':selected').attr('data-id');
            var role = "Big Boss";
            $.get({
                url: 'addusertoorga.php',
                data: {
                    iduser: iduser,
                    role: role,
                    idorganization: idorga
                },
                success: refreshuser
            });
        }
        else
            alert('Selectionner une personne a ajouter!')
    }

    function refreshuser() {
        $('#select-futur-BigBoss').load('select_usersnotinorga.php');
    }

    function DeleteOrganization() {
        var id = $('#id').val();
        $.get(
            {
                url:'deleteorganization.php',
                data:{
                    id:id
                },
                success:function () {
                    $.get(
                        {
                            url: 'select_organization.php',
                            datatype:'html',
                            success:function (html) {
                                $('#select-organizations').replaceWith(html);
                                $('#name').val("");
                                showformSelect();
                            }
                        }
                    )
                }
            }
        )
    }

    function showform() {
        $('#name').val('');
        $('#id').val('');
        $('#formulaire').css('display','block');
        $('#button-delete-Orga').css('display', 'none');
        $('#button-addUpdate-Orga').html('Ajouter');
    }

    function showformSelect(){
        var select = $("#select-organizations");
        var index = select.prop('selectedIndex');
        var id = '';
        var name = '';
        var display = 'none';
        if(index>0){
            id = select.find(':selected').attr('data-id');
            name = select.find(':selected').attr('data-name');
            display = 'block';
        }
        $('#name').val(name);
        $('#id').val(id);
        $('#formulaire').css('display',display);
        $('#button-delete-Orga').css('display', display);
        $('#div-select-bigBoss').css('display', display);
        $('#button-addUpdate-Orga').html('Update');

    }


    document.getElementById("name").addEventListener('keyup', function (event) {
        document.getElementById('nameEmpty').innerHTML = "";
        document.getElementById('nameAlreadyExist').innerHTML = "";
    });

    function addUpdateOrganization() {
        var id = $('#id').val();
        var name = $('#name').val();
        $.get(
            {
                url:'addorupdateorganization.php',
                data:{
                    id:id,
                    name:name
                },
                dataType:'json',
                success:function (json) {
                    if(Object.keys(json).length===0){
                        $.get(
                            {
                                url: 'select_organization.php',
                                datatype:'html',
                                success:function (html) {
                                    $('#select-organizations').replaceWith(html);
                                    $('#name').val("");
                                    showformSelect();
                                }
                            }
                        )
                    }
                    else {
                        for(var key in json) {
                            $('#' + key).html(json[key])
                        }
                    }
                }
            }
        )
    }


</script>