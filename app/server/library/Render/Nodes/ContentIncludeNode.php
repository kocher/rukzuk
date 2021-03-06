<?php

namespace Render\Nodes;

use Render\InfoStorage\ContentInfoStorage\IContentInfoStorage;
use Render\InfoStorage\ContentInfoStorage\Exceptions\TemplateDoesNotExists;

class ContentIncludeNode extends DynamicHTMLNode
{
  /**
   * Returns the content to include.
   *
   * @param IContentInfoStorage $contentInfoStorage
   * @return array
   */
  public function getContentInclude(IContentInfoStorage $contentInfoStorage)
  {
    if (!$this->hasIncludeTemplateId())
    {
      return array();
    }

    try {
      $templateContent = $contentInfoStorage->getTemplateContent($this->getIncludeTemplateId());
    } catch (TemplateDoesNotExists $doNothing) {
      return array();
    }

    if (isset($templateContent['children']) && is_array($templateContent['children']))
    {
      return $templateContent['children'];
    }
    return array();
  }

  /**
   * @return array
   */
  public function getContentIncludeIds()
  {
    $contentIncludeIds = array();
    if ($this->hasIncludeTemplateId())
    {
      $contentIncludeIds[] = $this->getIncludeTemplateId();
    }
    return $contentIncludeIds;
  }

  /**
   * Returns the id of the template that should be included.
   *
   * @return string
   */
  protected function getIncludeTemplateId()
  {
    $formValues = $this->getUnit()->getFormValues();
    if ($this->hasIncludeTemplateId()) {
      return $formValues[$this->getTemplateIdVariableName()];
    } else {
      return '';
    }
  }

  /**
   * @return bool
   */
  protected function hasIncludeTemplateId()
  {
    $variableName = $this->getTemplateIdVariableName();
    $formValues = $this->getUnit()->getFormValues();
    return (isset($formValues[$variableName])
      && is_string($formValues[$variableName])
      && !empty($formValues[$variableName]));
  }

  /**
   * @return string
   */
  protected function getTemplateIdVariableName()
  {
    return 'includeTemplateId';
  }
}
