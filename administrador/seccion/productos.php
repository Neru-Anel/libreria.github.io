<!--crud para agregar,modificar y eliminar libros-->
<!--$sentenciaSQL= $conexion->prepare("INSERT INTO libros (:ID, :Titulo, :Autor, :Imagen) VALUES ('9786073813501', 'Lujuria', 'Eva Muñoz', 'imagen.jpg');");-->
<?php include("../template/cabecera.php"); ?>
<?php 
$txtID=(isset($_POST['txtID']))?$_POST['txtID']:"";
$txtTitulo=(isset($_POST['txtTitulo']))?$_POST['txtTitulo']:"";
$txtAutor=(isset($_POST['txtAutor']))?$_POST['txtAutor']:"";
$txtImagen=(isset($_FILES['txtImagen']['name']))?$_FILES['txtImagen']['name']:"";
$accion=(isset($_POST['accion']))?$_POST['accion']:"";

include("../config/bd.php");

switch($accion){

    case "Agregar":
		$sentenciaSQL= $conexion->prepare("INSERT INTO libros (Titulo, Autor, Imagen) VALUES (:Titulo, :Autor, :Imagen);");
        $sentenciaSQL->bindParam(':Titulo',$txtTitulo);
        $sentenciaSQL->bindParam(':Autor',$txtAutor);

        $fecha=new DateTime(); 
        $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp()."_".$_FILES["txtImagen"]["name"]:"imagen.jpg";
        
        $temporalImagen=$_FILES["txtImagen"]["temporal_name"];

        if($temporalImagen!=""){
            move_uploaded_file($temporalImagen,"../../img/".$nombreArchivo);
        }
        $sentenciaSQL->bindParam(':Imagen',$txtImagen);
        $sentenciaSQL->execute();
		header("Location:productos.php");
        break;

    case "Modificar":
        $sentenciaSQL= $conexion->prepare("UPDATE libros SET Titulo=:Titulo WHERE id=:ID"); 
        $sentenciaSQL->bindParam(':Titulo',$txtTitulo);
        $sentenciaSQL->bindParam(':ID',$txtID);
        $sentenciaSQL->execute();

        if($txtImagen=""){

            $fecha=new DateTime(); 
            $nombreArchivo=($txtImagen!="")?$fecha->getTimestamp().""-$_FILES["txtImagen"]["name"]:"imagen.jpg";
            $temporalImagen=$_FILES["txtImagen"]["temporal_name"];
            
            move_uploaded_file($temporalImagen,"../../img/".$nombreArchivo);
            $sentenciaSQL= $conexion->prepare("SELECT FROM libros WHERE id=:ID"); 
            $sentenciaSQL->bindParam(':ID',$txtID);
            $sentenciaSQL->execute();
            $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
        if(isset($libro["image"]) &&($libro["imagen"]!="imagen.jpg")){
           if(file_exists("../../img/".$libro["imagen"])){
            unlink("../../img/".$libro["imagen"]);
           } 
        }
            $sentenciaSQL= $conexion->prepare("UPDATE libros SET Imagen=:Imagen WHERE id=:ID"); 
            $sentenciaSQL->bindParam(':Imagen',$nombreArchivo);
            $sentenciaSQL->bindParam(':ID',$txtID);
            $sentenciaSQL->execute();  

            
        }

        break;

    case "Cancelar":
        header("Location:productos.php");
        break;

    case "Seleccionar":
        $sentenciaSQL= $conexion->prepare("SELECT *FROM libros WHERE id=:ID"); 
        $sentenciaSQL->bindParam(':ID',$txtID);
        $sentenciaSQL->execute();
        $listaLibros=$sentenciaSQL->fetch(PDO::FETCH_LAZY);

        $txtTitulo=$libro['titulo'];
        $txtAutor=$libro['autor'];
        $txtImagen=$libro['imagen'];
        break;
    
    case "Borrar":
        $sentenciaSQL= $conexion->prepare("SELECT FROM libros WHERE id=:ID"); 
        $sentenciaSQL->bindParam(':ID',$txtID);
        $sentenciaSQL->execute();
        $libro=$sentenciaSQL->fetch(PDO::FETCH_LAZY);
        if(isset($libro["image"]) &&($libro["imagen"]!="imagen.jpg")){
           if(file_exists("../../img/".$libro["imagen"])){
            unlink("../../img/".$libro["imagen"]);
           } 
        }

        $sentenciaSQL= $conexion->prepare("DELETE FROM libros WHERE id=:ID"); 
        $sentenciaSQL->bindParam(':ID',$txtID);
        $sentenciaSQL->execute();
        break;
}

$sentenciaSQL= $conexion->prepare("SELECT *FROM libros"); 
$sentenciaSQL->execute(); 
$listaLibros=$sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="col-md-5">
    
    <div class="card">
        <div class="card-header">
            Datos del libro
        </div>

        <div class="card-body">
                 <form method="POST" enctype="multipart/form-data">
                 <div class = "form-group">
                 <label for="txtID">ID:</label>
                 <input type="text" required readonly class="form-control" name="txtID" value="<?php echo $txtID;?>" id="txtID"  placeholder="ID">
                 </div>

                 <div class = "form-group">
                 <label for="txtTitulo">Título:</label>
                 <input type="text" required class="form-control" name="txtTitulo" value="<?php echo $txtTitulo;?>" id="txtTitulo"  placeholder="Ingresa el título del libro">
                 </div>

                 <div class = "form-group">
                 <label for="txtAutor">Autor:</label>
                 <input type="text" required class="form-control" name="txtAutor" value="<?php echo $txtAutor;?>" id="txtAutor"  placeholder="Ingresa el nombre del Autor">
                 </div>

                 <div class = "form-group">
                 <label for="txtImagen">Imagen:</label>

                 <br/>
                 <?php if($txtImagen!=""){?>
                    <img class="img-thumbnail rounded" src="../../img/<?php echo $txtImagen;?>" width="50">
                 <?php }?>

                 <input type="file" required class="form-control" name="txtImagen" id="txtImagen"  placeholder="">
                 </div>

                 <div class="btn-group" role="group" aria-label="">
                 <button type="submit" name="accion" <?php echo ($accion=="Seleccionar")?"disabled":"";?> value="Agregar" class="btn btn-success">Agregar</button>
                 <button type="submit" name="accion" <?php echo ($accion!="Seleccionar")?"disabled":"";?> value="Modificar" class="btn btn-primary">Modificar</button>
                 <button type="submit" name="accion" <?php echo ($accion!="Seleccionar")?"disabled":"";?> value="Cancelar" class="btn btn-danger">Cancelar</button>
                 </div>

             </form>
        </div>

    </div>


 </div>
 <div class="col-md-7">
 <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($listaLibros as $libro){  ?>
            <tr>
                <td><?php echo $libro ['ID'];?></td>
                <td><?php echo $libro ['Titulo'];?></td>
                <td><?php echo $libro ['Autor'];?></td>
                <td>
                    
                    <img class="img-thumnail rounded" src="../../img/<?php echo $libro ['Imagen'];?>" width="50">
                
                </td>

                <td>
                <form method="POST">
					<input type="hidden" class="form-control" name="txtID" id="txtID" value="<?php echo $libro['ID'];?>">
							
					<input type="submit" class="btn btn-primary" name="action" value="Seleccionar"/>
					<input type="submit" class="btn btn-danger" name="action" value="Borrar"/>
				</form>
                </td>
            </tr>
<?php }?>



        </tbody>
    </table>
 </div>

<?php include("../template/pie.php"); ?>


