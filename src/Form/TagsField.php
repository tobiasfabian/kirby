<?php

namespace Kirby\Form;

use Kirby\Toolkit\V;
use Kirby\Util\Str;

use Kirby\Exception\InvalidArgumentException;

class TagsField extends Field
{
    use Mixins\Converter;
    use Mixins\Help;
    use Mixins\Icon;
    use Mixins\Label;
    use Mixins\Options;
    use Mixins\Required;
    use Mixins\Separator;
    use Mixins\Value;

    protected $accept;

    public function accept(): string
    {
        return $this->accept;
    }

    protected function defaultAccept(): string
    {
        return 'all';
    }

    protected function defaultIcon(): string
    {
        return 'tag';
    }

    protected function defaultLabel(): string
    {
        return 'Tags';
    }

    protected function defaultName(): string
    {
        return 'tags';
    }

    public function setAccept(string $accept)
    {
        if (V::in($accept, ['all', 'options']) === false) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid value for the "accept" option', $accept));
        }

        return $this->accept = $accept;
    }

    protected function validate($value): bool
    {
        $this->validateRequired($value);

        return true;
    }

    protected function valueFromInput($input)
    {

        // if $input is string, convert it to array
        $value = $this->valueFromList($input, $this->separator());

        // transform into value-text objects
        $value = array_map(function ($tag) {
            $option = $this->option($tag['value'] ?? $tag);

            return [
                'value' => $tag['value'] ?? $tag,
                'text' => $option['text'] ?? $tag['text'] ?? $tag,
            ];
        }, $value);

        return $value;
    }

    protected function valueToString($value)
    {
        $value = array_column($value, 'value');
        return $this->valueToList($value, $this->separator() . ' ');
    }
}
