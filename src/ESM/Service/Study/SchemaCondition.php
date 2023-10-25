<?php

namespace App\ESM\Service\Study;

use App\ESM\Entity\Patient;
use App\ESM\Entity\PatientData;
use App\ESM\Entity\PatientVariable;
use App\ESM\Entity\Project;
use App\ESM\Entity\VariableOption;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\String\u;

class SchemaCondition
{
    public const MAIN_TABLE = 'patient_data';

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var Patient[]|object[]
     */
    private $patients;

	/**
	 * @param ManagerRegistry $managerRegistry
	 */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry 	= $managerRegistry;
    }

    /**
     * Liste des filtres en fonctoion des variables.
     */
    public function getFilters(Project $project): array
    {
        $variables = $this->managerRegistry->getRepository(PatientVariable::class)
            ->findBy([
                'project' => $project,
                'deletedAt' => null,
            ]);

        $data = [];
        foreach ($variables as $variable) {
            $variable_type = $variable->getVariableType()->getLabel();
            // Constante
            $data[] = [
                'id' => $variable->getId().'_ct',
                'label' => $variable->getLabel().' vs constante',
                'type' => $this->getType($variable_type),
                'vartype' => $variable_type,
                'input' => $this->getInput($variable_type),
                'values' => $this->getConstantValues($variable), //  from variable_option
                'operators' => $this->getOperators($variable_type),
            ];

            // Variable
            $data[] = [
                'id' => $variable->getId().'_var',
                'label' => $variable->getLabel().' vs variable',
                'type' => $this->getType($variable_type),
                'vartype' => $variable_type,
                'input' => $this->getInput($variable_type, 'variable'),
                'values' => $this->getVariablesValues($variable), // from patient_variable
                'operators' => $this->getOperators($variable_type),
            ];
        }

        return $data;
    }

    /**
     * Champ de la variable en fonction de son format.
     */
    public function getInput(string $input, string $comparateur = 'constante'): ?string
	{
        switch ($input) {
            case 'list':
            case 'date' && 'variable' === $comparateur:
                return 'select';
        }

        return null;
    }

    /**
     * Propriété de la variable en fonction de son format.
     */
    private function getType(string $input): string
    {
        switch ($input) {
            case 'list':
            case 'numeric':
                return 'integer';
        }

        return $input;
    }

	/**
	 * @param PatientVariable $variable
	 * @return array
	 */
    private function getConstantValues(PatientVariable $variable): array
    {
        $type = $variable->getVariableType()->getLabel();
        if (null === $type || in_array($type, ['string', 'numeric', 'date'])) {
            return [];
        }

        $variable_options = $this->managerRegistry->getRepository(VariableOption::class)
            ->findBy([
                'list' => $variable->getVariableList(),
            ], ['code' => 'DESC']);

        if (null === $variable_options) {
            return [];
        }

        $data = [];
        foreach ($variable_options as $variable_option) {
            $option_id = $variable_option->getId();
            $option_label = $variable_option->getLabel();

            $data[$option_id] = $option_label.'('.$variable_option->getCode().')';
        }

        // liste des variable_option si type list
        return $data;
    }

	/**
	 * @param PatientVariable $variable
	 * @return array
	 */
    public function getVariablesValues(PatientVariable $variable): array
    {
        $other_variables = $this->managerRegistry->getRepository(PatientVariable::class)->findBy([
            'variableType' => $variable->getVariableType(),
            'project' => $variable->getProject(),
            'deletedAt' => null,
        ]);

        $data = [];
        foreach ($other_variables as $other_variable) {

            // exclude current variable
            if ('list' === $other_variable->getVariableType()->getLabel()) {
                if ($other_variable->getId() !== $variable->getId() && $other_variable->getVariableList()->getId() === $variable->getVariableList()->getId()) {
                    $data[$other_variable->getId()] = $other_variable->getLabel();
                }
            } else {
                if ($other_variable->getId() !== $variable->getId()) {
                    $data[$other_variable->getId()] = $other_variable->getLabel();
                }
            }
        }

        return $data;
    }

    /**
     * Liste des Opérateurs en fonction du type de variable.
     */
    private function getOperators(string $input): array
    {
        switch ($input) {

            case 'list':
            case 'numeric':
            case 'date':
                return [
                    'equal',
                    'not_equal',
                    'greater',
                    'greater_or_equal',
                    'less',
                    'less_or_equal',
                    'is_null',
					'is_not_null'
                ];

            case 'string':
                return [
                    'equal',
                    'not_equal',
                    'is_null',
					'is_not_null'
                ];
        }

        return [];
    }

	/**
	 * Liste des patients qui entrent dans les conditions paramétrées.
	 * @throws \JsonException
	 */
    public function getPatients(\App\Entity\SchemaCondition $condition): array
    {
        // decode JSON
        $json = \json_decode($condition->getCondition(), true, 512, JSON_THROW_ON_ERROR);

        $this->patients = $this->managerRegistry->getRepository(Patient::class)->findBy([
            'project' => $condition->getProject(),
        ]);

        $rules = $json['rules'] ?? [];
        $condition = $json['condition'] ?? '';

        // parcourir les regles pour extraire les resultats de la condition
        $data = $this->parseRule($rules);

        $patients_id = [];
        foreach ($data as $patient => $items) {

            // Le nombre total de conditions a verifier est egal au nombre total de conditions verifiees
            // Garder le patient_data.id
            if ('AND' === $condition && count($rules) === array_sum($data[$patient]['condition_true'])) {
                $patients_id[] = $patient;
            }

            // Condition "OR", au moins une condition est vraie
            if ('OR' === $condition) {

                foreach ($data[$patient]['condition_true'] as $variable_id => $condition_true) {
                    if (0 < $condition_true) {
                        $patients_id[] = $patient;
                    }
                }
            }
        }

        return array_unique($patients_id);
    }

    /**
     * Données du patient en fonction de la variable.
     */
    private function getDataVariable(Patient $patient, int $variable_id): PatientData
    {
        return $this->managerRegistry->getRepository(PatientData::class)->findOneBy([
            'patient' 	=> $patient,
            'variable' 	=> $variable_id,
        ]);
    }

    /**
     * parcourir les regles pour extraire les resultats de la condition.
     */
    private function parseRule(array $rules): array
    {
        $data = [];

        foreach ($rules as $rule) {

            $rule_field = $rule['field'];

            // Variable
            $is_variable = u($rule_field)->containsAny('_var');

            $field = $rule_field;
            $type = $rule['type'];
            $operator = $rule['operator'];
            $value = $rule['value'];

            $field = str_replace('_ct', '', $field);
            $field = str_replace('_var', '', $field);
            $id = (int) $field;

            $condition_true_count = 0;

            // Patients inside project
            foreach ($this->patients as $patient) {

                $patient_id = $patient->getId();

                $patients_data = $this->getDataVariable($patient, $id);

                if (null !== $patients_data) {

                    $variable_value = $patients_data->getVariableValue();

                    // Remplacer "variable_value" dans le cas d'une variable
                    if ($is_variable) {

                        $patient_data_variable = $this->getDataVariable($patient, (int) $rule['value']);
                        if (null === $value) {
                            $value = null;
                        } else {
                            $value = null !== $patient_data_variable ? $patient_data_variable->getVariableValue() : '';
                        }
                    }

                    // convertir en lower si type = string afin de faciliter la comparaison
                    if ('string' === $type) {
                        $value = (null === $value) ? null : u($value)->lower()->toString();
                        $variable_value = u($variable_value)->lower()->toString();
                    }

                    // Nombre de conditions qui repondent true
                    $condition_true_count = $this->countResultConditions($operator, $variable_value, $value);
                }
                $data[$patient_id]['condition_true'][$id] = $condition_true_count;
                $data[$patient_id]['data'][$id] = $patients_data->getId();
                $condition_true_count = 0;
            }
        }

        return $data;
    }

    /**
     * Compare 2 valeurs - incremente de 1 si la comparaison est "true" et 0 sinon.
     */
    private function countResultConditions(string $operator, string $variable_value, $value = null): int
	{
        $condition_true_count = 0;

        switch ($operator) {

            case 'equal':

                if ((!empty($variable_value) && ('' != $value)) && ($variable_value == $value)) {
                    ++$condition_true_count;
                }
                break;

			case 'not_equal':

                if ((!empty($variable_value) && ('' != $value)) && ($variable_value != $value)) {
                    ++$condition_true_count;
                }
                break;

			case 'contains':

                if ((!empty($variable_value) && ('' != $value)) && (u($variable_value)->containsAny($value))) {
                    ++$condition_true_count;
                }
                break;

			case 'begins_with':

                if ((!empty($variable_value) && ('' != $value)) && (u($variable_value)->startsWith($value))) {
                    ++$condition_true_count;
                }
                break;

			case 'ends_with':

                if ((!empty($variable_value) && ('' != $value)) && (u($variable_value)->endsWith($value))) {
                    ++$condition_true_count;
                }
                break;

            case 'greater':

                if ((!empty($variable_value) && ('' != $value)) && ($variable_value > $value)) {
                    ++$condition_true_count;
                }
                break;

			case 'greater_or_equal':

                if ((!empty($variable_value) && ('' != $value)) && ($variable_value >= $value)) {
                    ++$condition_true_count;
                }
                break;

			case 'less':

                if ((!empty($variable_value) && ('' != $value)) && ($variable_value < $value)) {
                    ++$condition_true_count;
                }
                break;

			case 'less_or_equal':

                if ((!empty($variable_value) && ('' != $value)) && ($variable_value <= $value)) {
                    ++$condition_true_count;
                }
                break;

			case 'is_null':

                if (empty($variable_value) && is_null($value)) {
                    ++$condition_true_count;
                }
                break;

			case 'is_not_null':

				if (empty($variable_value) && !is_null($value)) {
					++$condition_true_count;
				}
				break;
        }

        return $condition_true_count;
    }
}
