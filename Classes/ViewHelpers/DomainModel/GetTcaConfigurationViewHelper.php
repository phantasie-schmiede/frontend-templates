<?php
declare(strict_types=1);
namespace PSB\PsbTemplates\ViewHelpers\DomainModel;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Daniel Ablass <dn@phantasie-schmiede.de>, PSbits
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use PSB\PsbFoundation\Exceptions\AnnotationException;
use PSB\PsbFoundation\Service\Configuration\Fields;
use PSB\PsbFoundation\Traits\InjectionTrait;
use PSB\PsbFoundation\Utility\ExtensionInformationUtility;
use ReflectionClass;
use ReflectionException;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Extbase\Security\Exception\InvalidArgumentForHashGenerationException;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetTcaConfigurationViewHelper
 *
 * @package PSB\PsbTemplates\ViewHelpers\DomainModel
 */
class GetTcaConfigurationViewHelper extends AbstractViewHelper
{
    use InjectionTrait;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('domainModel', 'string', 'full qualified class name', true,);
        $this->registerArgument('properties', 'string', 'variable name for the result', false, 'properties');
    }

    /**
     * @throws AnnotationException
     * @throws Exception
     * @throws InvalidArgumentForHashGenerationException
     * @throws ReflectionException
     */
    public function render(): void
    {
        $tableName = ExtensionInformationUtility::convertClassNameToTableName($this->arguments['domainModel']);
        $configuration = $GLOBALS['TCA'][$tableName]['columns'];
        $domainModelReflection = $this->get(ReflectionClass::class, $this->arguments['domainModel']);
        $properties = $domainModelReflection->getProperties();
        $result = [];

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $columnName = ExtensionInformationUtility::convertPropertyNameToColumnName($propertyName);

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
                        $records = $this->get($repositoryClassName)->findTcaSelectItems();

                        foreach ($records as $record) {
                            $result[$propertyName]['items'][$record->getUid()] = $record->getDisplayName();
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
