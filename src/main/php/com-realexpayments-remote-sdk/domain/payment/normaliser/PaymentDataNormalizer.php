<?php


namespace com\realexpayments\remote\sdk\domain\payment\normaliser;

use com\realexpayments\remote\sdk\domain\CvnNumber;
use com\realexpayments\remote\sdk\domain\PaymentData;
use com\realexpayments\remote\sdk\SafeArrayAccess;
use com\realexpayments\remote\sdk\utils\NormaliserHelper;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class PaymentDataNormalizer implements NormalizerInterface, DenormalizerInterface, DenormalizerAwareInterface {
    use SerializerAwareTrait;
    use DenormalizerAwareTrait;

	private $format;
	private $context;

	/**
	 * Normalizes an object into a set of arrays/scalars.
	 *
	 * @param object $object object to normalize
	 * @param string $format format the normalization result will be encoded as
	 * @param array $context Context options for the normalizer
	 *
	 * @return array|string|bool|int|float|null
	 */
	public function normalize( $object, $format = null, array $context = array() ) {
		/** @var PaymentData $object */

		return array_filter( array(
			'cvn' => $object->getCvnNumber()
		), array( NormaliserHelper::GetClassName(), "filter_data" ) );
	}

	/**
	 * Checks whether the given class is supported for normalization by this normalizer.
	 *
	 * @param mixed $data Data to normalize.
	 * @param string $format The format being (de-)serialized from or into.
	 *
	 * @return bool
	 */
	public function supportsNormalization( $data, $format = null ) {
		if ( $format == "xml" && $data instanceof PaymentData ) {
			return true;
		}

		return false;
	}

	/**
	 * Denormalizes data back into an object of the given class.
	 *
	 * @param mixed $data data to restore
	 * @param string $class the expected class to instantiate
	 * @param string $format format the given data was extracted from
	 * @param array $context options available to the denormalizer
	 *
	 * @return object
	 */
	public function denormalize( $data, $class, $format = null, array $context = array() ) {
		if ( is_null( $data ) ) {
			return null;
		}

		$this->format  = $format;
		$this->context = $context;

		$data = new SafeArrayAccess( $data );

		$paymentData = new PaymentData();
		$paymentData->addCvnNumber( $this->denormalizeCvn( $data));

		return $paymentData;

	}

	private function denormalizeCvn( $data ) {
		return $this->denormalizer->denormalize( $data['cvn'], CvnNumber::GetClassName(), $this->format, $this->context );
	}

	/**
	 * Checks whether the given class is supported for denormalization by this normalizer.
	 *
	 * @param mixed $data Data to denormalize from.
	 * @param string $type The class to which the data should be denormalized.
	 * @param string $format The format being deserialized from.
	 *
	 * @return bool
	 */
	public function supportsDenormalization( $data, $type, $format = null ) {
		if ( $format == "xml" && $type == PaymentData::GetClassName()) {
			return true;
		}
		return false;
	}
}