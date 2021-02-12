<?php
declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace PSB\PsbTemplates\ViewHelpers\DomainModel;

use PSB\PsbFoundation\Domain\Repository\AbstractRepository;
use PSB\PsbFoundation\Service\Configuration\Fields;
use PSB\PsbFoundation\Traits\Properties\TcaServiceTrait;
use ReflectionClass;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetTcaConfigurationViewHelper
 *
 * @package PSB\PsbTemplates\ViewHelpers\DomainModel
 */
class GetTcaConfigurationViewHelper extends AbstractViewHelper
{
    use TcaServiceTrait;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('domainModel', 'string', 'full qualified class name', true);
        $this->registerArgument('properties', 'string', 'variable name for the result', false, 'properties');
    }

    public function render(): void
    {
        $tableName = $this->tcaService->convertClassNameToTableName($this->arguments['domainModel']);
        $configuration = $GLOBALS['TCA'][$tableName]['columns'];
        $domainModelReflection = GeneralUtility::makeInstance(ReflectionClass::class, $this->arguments['domainModel']);
        $properties = $domainModelReflection->getProperties();
        $result = [];

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $columnName = $this->tcaService->convertPropertyNameToColumnName($propertyName);

            if (true === $configuration[$columnName]['config']['editableInFrontend']) {
                $result[$propertyName] = $configuration[$columnName]['config'];

                if (Fields::FIELD_TYPES['INLINE'] === $configuration[$columnName]['config']['type']
                    && isset($result[$propertyName]['linked_model'])
                ) {
                    $linkedModelClassNameParts = explode('\\', $result[$propertyName]['linked_model']);
                    $result[$propertyName]['linkedModelShortName'] = array_pop($linkedModelClassNameParts);
                }

                if (Fields::FIELD_TYPES['SELECT'] === $configuration[$columnName]['config']['type']
                    || Fields::FIELD_TYPES['MM'] === $configuration[$columnName]['config']['type']
                ) {
                    if (isset($result[$propertyName]['items'][0]) && is_array($result[$propertyName]['items'][0])) {
                        // Convert TCA item structure to a frontend compatible array (remove one dimension).
                        $items = [];

                        foreach ($result[$propertyName]['items'] as $item) {
                            $items[$item[0]] = $item[1];
                        }

                        $result[$propertyName]['items'] = $items;
                    }

                    if (isset($result[$propertyName]['linked_model'])) {
                        $repositoryClassName = ClassNamingUtility::translateModelNameToRepositoryName($result[$propertyName]['linked_model']);
                        $repository = GeneralUtility::makeInstance($repositoryClassName);

                        if ($repository instanceof AbstractRepository) {
                            $records = $repository->findTcaSelectItems();

                            foreach ($records as $record) {
                                $result[$propertyName]['items'][$record->getUid()] = $record->getDisplayName();
                            }
                        }
                    }
                }

                // To match partial names:
                $result[$propertyName]['type'] = ucfirst($result[$propertyName]['type']);
            }
        }

        $this->renderingContext->getVariableProvider()->add($this->arguments['properties'], $result);
    }
}
