import 'package:flutter/material.dart';
class CardSlider extends StatefulWidget {
  final List<Map<String, String>> Cardlist;

  const CardSlider({Key? key, required this.Cardlist}) : super(key: key);

  @override
  _CardSliderState createState() => _CardSliderState();
}
class _CardSliderState extends State<CardSlider> {
  final int maxVisibleDots = 5;
  late final ScrollController _scrollController;
  // This value will represent the active card index (it can be fractional during scrolling).
  double _currentPage = 0;

  // Card width as defined in _buildCard.
  final double cardWidth = 220;
  // The horizontal margin from _buildCard (8 left + 8 right).
  final double horizontalMargin = 16;

  double get effectiveCardWidth => cardWidth + horizontalMargin;

  @override
  void initState() {
    super.initState();
    _scrollController = ScrollController()
      ..addListener(() {
        // Calculate the active card index.
        // Adding half the effective width to determine which card is centered.
        double page = (_scrollController.offset + (effectiveCardWidth / 2)) /
            effectiveCardWidth;
        setState(() {
          _currentPage = page;
        });
      });
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // Container height for the entire card slider.
    double containerHeight = 380;
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        SizedBox(
          height: containerHeight,
          child: ListView.builder(
            controller: _scrollController,
            scrollDirection: Axis.horizontal,
            itemCount: widget.Cardlist.length,
            itemBuilder: (context, index) {
              return _buildCard(widget.Cardlist[index], containerHeight);
            },
          ),
        ),
        const SizedBox(height: 10),
        _buildPagination(widget.Cardlist.length),
      ],
    );
  }

  Widget _buildCard(Map<String, String> product, double containerHeight) {
    // Use the fixed card width defined above.
    double imageHeight = containerHeight - 100;
    return SizedBox(
      width: cardWidth,
      child: Container(
        margin:
            const EdgeInsets.only(top: 35, left: 8, right: 8, bottom: 5), // margin = 16 total horizontal
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(16),
          boxShadow: const [
            BoxShadow(
              color: Colors.grey,
              blurRadius: 5,
            ),
          ],
        ),
        child: Column(
          children: [
            Stack(
              clipBehavior: Clip.none,
              children: [
                ClipRRect(
                  borderRadius: const BorderRadius.only(
                      topLeft: Radius.circular(16),
                      topRight: Radius.circular(16)),
                  child: Image.network(
                    product["product_image"] ?? "",
                    height: imageHeight,
                    width: cardWidth,
                    fit: BoxFit.cover,
                  ),
                ),
                if (product["vendor_image"] != null)
                  Positioned(
                    top: -30,
                    left: 0,
                    right: 0,
                    child: CircleAvatar(
                      radius: 35,
                      backgroundColor: Colors.white,
                      child: CircleAvatar(
                        radius: 32,
                        backgroundImage:
                            NetworkImage(product["vendor_image"] ?? ""),
                      ),
                    ),
                  ),
                Positioned(
                  top: 8,
                  right: 8,
                  child: CircleAvatar(
                    radius: 14,
                    backgroundColor:
                        const Color.fromARGB(214, 158, 158, 158),
                    child: const Icon(Icons.add,
                        size: 20, color: Colors.white),
                  ),
                ),
                Positioned(
                  bottom: 0,
                  left: 0,
                  right: 0,
                  child: Container(
                    height: 40,
                    decoration: const BoxDecoration(
                      color: Color.fromARGB(124, 0, 0, 0),
                    ),
                    child: Center(
                      child: Text(
                        product["product_name"] ?? "", 
                        style: const TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                        ),
                      ),
                    ),
                  ),
                )
              ],
            ),
            Expanded(
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 15),
                decoration: const BoxDecoration(
                  borderRadius: BorderRadius.only(
                      bottomLeft: Radius.circular(16),
                      bottomRight: Radius.circular(16)),
                  color: Colors.white,
                ),
                width: double.infinity,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _discountIcon(
                        discountText: product["discount"] ?? '0%',
                        IconColor: Colors.red[700] ?? Colors.red,
                        IconSize: 40,
                        TextColor: Colors.white,
                        FontSize: 9),
                    Text(
                      "\$${product["price"]}",
                      style: TextStyle(
                        color: Colors.grey[600],
                        fontSize: 14,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _discountIcon({
    required String discountText,
    required Color IconColor,
    required double IconSize,
    required Color TextColor,
    required double FontSize,
  }) {
    return Stack(
      alignment: Alignment.center,
      children: [
        Icon(Icons.sell, size: IconSize, color: IconColor),
        Text(
          discountText,
          style: TextStyle(
            color: TextColor,
            fontSize: FontSize,
            fontWeight: FontWeight.bold,
          ),
        ),
      ],
    );
  }

  Widget _buildPagination(int totalCards) {
    // Round the current page to determine the active card index.
    int activeIndex = _currentPage.round();

    // Generate a dot for each card.
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: List.generate(totalCards, (index) {
        bool isActive = index == activeIndex;
        double dotWidth = isActive ? 16 : 8;
        return AnimatedContainer(
          duration: const Duration(milliseconds: 300),
          margin: const EdgeInsets.symmetric(horizontal: 4),
          width: dotWidth,
          height: 8,
          decoration: BoxDecoration(
            color: isActive ? Colors.black : Colors.grey[300],
            borderRadius: BorderRadius.circular(4),
          ),
        );
      }),
    );
  }
}