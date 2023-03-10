
<?php
require_once("./conf/conf.php");

require_once("./models/projectModel.php");
require_once("./models/pictureModel.php");
require_once("./models/skillModel.php");

class ProjectController{
    public function readAll(): array
    {
        global $pdo;
        $sql = "SELECT * FROM project";
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_CLASS, "ProjectModel");

        foreach($result as $project){
            $this->loadSkillsFromProject($project);
        }

        return $result;
    }

    public function readOne(int $id): ProjectModel {
        global $pdo;

        // Requête de récupération du projet
        $sql = "SELECT * FROM project WHERE id_project = :id";

        $statement = $pdo->prepare($sql);
        $statement->bindParam(":id", $id, PDO::PARAM_INT);
        $statement->execute();

        $statement->setFetchMode(PDO::FETCH_CLASS, "ProjectModel");
        $result = $statement->fetch();

        // Requête de récupération des images
        $sql = "SELECT * FROM picture WHERE id_project = :id";

        $statement = $pdo->prepare($sql);
        $statement->bindParam(":id", $id, PDO::PARAM_INT);
        $statement->execute();

        $result->pictures = $statement->fetchAll(PDO::FETCH_CLASS, "PictureModel");

        // Requête de récupération des skills
        $this->loadSkillsFromProject($result);

        return $result;
    }

    public function loadSkillsFromProject(ProjectModel $project)
    {
        global $pdo;

        $sql = "SELECT skill.id_skill, skill.name, skill.level, skill.picture
        FROM skill
        JOIN skill_project ON skill_project.id_skill = skill.id_skill
        JOIN project ON project.id_project = skill_project.id_project
        WHERE project.id_project = :id";

        $statement = $pdo->prepare($sql);
        $statement->bindParam(":id", $project->id_project, PDO::PARAM_INT);
        $statement->execute();

        $project->skills = $statement->fetchAll(PDO::FETCH_CLASS, "SkillModel");
    }
}
?>