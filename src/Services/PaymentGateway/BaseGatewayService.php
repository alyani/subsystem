<?php

namespace Alyani\Subsystem\Services\PaymentGateway;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

abstract class BaseGatewayService
{
    protected array $_config = [];
    protected int $amount = 0;
    protected string $currency = '';
    protected int $paymentID = 0;
    protected string $callbackURL = '';
    protected string $authorizeToken = '';
    protected string $mobile = '';
    protected string $gatewayReference = '';
    protected string $cardNumber = '';
    protected string $orderDescription = '';
    protected bool $isSettled = false;

    /**
     * Checks the payment details provided in the request against the expected payment information.
     * Validates SaleOrderId, RefId, and ensures the response code indicates a successful transaction.
     * Logs errors and throws exceptions if any validations fail.
     *
     * @param Request $request The incoming request containing payment data.
     * @param object $payment The payment object with expected values for verification.
     * @return bool Returns true if all checks pass.
     */
    abstract public function checkPaymentForVerify($request, $payment);

    /**
     * Authorizes the payment process.
     * This method should be implemented in child classes to handle specific authorization logic.
     */
    abstract public function authorize();

    /**
     * Returns an array of payment gateway data including response code, card information, and sale reference ID.
     *
     * @return array The payment gateway data.
     */
    abstract public function paymentGatewayData(): array;

    /**
     * Prepares and returns the parameters required for redirecting to the payment gateway.
     * This method should be implemented in child classes to structure data specific to each gateway.
     *
     * @return array The parameters for redirection to the payment gateway.
     */
    abstract public function prepareRedirectParams();

    /**
     * Verifies the payment status and validity based on the provided request and payment ID.
     * Checks for the correct response code, validates the payment ID, and ensures the transaction is successful.
     * Logs any discrepancies and throws exceptions if verification fails.
     *
     * @throws Exception If the verification fails.
     */
    abstract public function verify();

    /**
     * Determines if the payment has been settled.
     *
     * @return bool Returns true if payment is settled ; false otherwise.
     */
    public function isSettled(): bool
    {
        return $this->isSettled;
    }

    /**
     * Determines if the application is running in sandbox mode.
     * Reads the configuration to check if the finance sandbox mode is enabled.
     *
     * @return bool Returns true if sandbox mode is enabled; false otherwise.
     */
    public function isSandbox(): bool
    {
        return (Config::get('subsystem.finance.sandbox', false));
    }

    /**
     * Sets a protected property dynamically using the provided key and value.
     * The method converts the key to lowercase for the property assignment.
     *
     * @param string $key The property name to set.
     * @param mixed $value The value to assign to the property.
     * @return self Returns the current instance for chaining.
     */
    protected function setAttribute(string $key, mixed $value): static
    {
        $this->{lcfirst($key)} = $value;
        return $this;
    }

    /**
     * Gets the value of a protected property dynamically based on the provided key.
     * The method converts the key to lowercase for the property access.
     *
     * @param string $key The property name to retrieve.
     * @return mixed The value of the specified property.
     */
    protected function getAttribute(string $key): mixed
    {
        return $this->{lcfirst($key)};
    }

    /**
     * Magic method to handle dynamic setting and getting of properties.
     * Allows calling setFieldName($value) or getFieldName() dynamically.
     *
     * @param string $method The method name being called.
     * @param array $arguments The arguments provided to the method.
     * @return mixed The result of the magicSet or magicGet method, or throws an exception if method is invalid.
     */
    public function __call(string $method, array $arguments)
    {
        preg_match('/^set(?P<field>\w+)$/', $method, $args);
        if ($args) {
            return $this->setAttribute($args['field'], $arguments[0]);
        }
        preg_match('/^get(?P<field>\w+)$/', $method, $args);
        if ($args) {
            return $this->getAttribute($args['field']);
        }
        throw new \BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
    }

    public static function traceLog($message, $params = [])
    {
        Log::debug($message, $params);
    }
}
