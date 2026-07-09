<?php

namespace Desino\McpBoilerplate\Support;

class RedirectHelper
{
    public static function addMessage(string $message, string $messageType = 'danger'): void
    {
        $helper = config('mcp-boilerplate.redirect_helper');

        if (is_string($helper) && class_exists($helper) && method_exists($helper, 'addRedirectMsg')) {
            $helper::addRedirectMsg($message, $messageType);

            return;
        }

        if ($message === '') {
            return;
        }

        $messageType = in_array($messageType, ['success', 'danger', 'warning', 'info'], true)
            ? $messageType
            : 'danger';

        $messages = session()->get('sys_messages.'.$messageType, []);
        $messages[] = $message;
        session()->put('sys_messages.'.$messageType, $messages);
    }

    /**
     * @return array<string, string>
     */
    public static function defaultValidationMessages(): array
    {
        $helper = config('mcp-boilerplate.redirect_helper');

        if (is_string($helper) && class_exists($helper) && method_exists($helper, 'defaultValidationMsgs')) {
            return $helper::defaultValidationMsgs();
        }

        return [
            'required' => __('messages.general_validations_messages_required'),
            'required_if' => __('messages.general_validations_messages_required'),
            'string' => __('messages.general_validations_messages_string'),
            'in' => __('messages.general_validations_messages_in'),
            'max' => __('messages.general_validations_messages_max'),
            'min' => __('messages.general_validations_messages_min'),
            'lte' => __('messages.general_validations_messages_lte'),
            'gte' => __('messages.general_validations_messages_gte'),
            'numeric' => __('messages.general_validations_messages_numeric'),
            'integer' => __('messages.general_validations_messages_integer'),
            'date_format' => __('messages.general_validations_messages_date_format'),
            'email' => __('messages.general_validations_messages_email'),
            'confirmed' => __('messages.general_validations_messages_confirmed'),
            'unique' => __('messages.general_validations_messages_unique'),
        ];
    }
}
