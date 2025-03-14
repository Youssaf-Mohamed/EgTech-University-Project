import 'package:flutter/material.dart';

class TextWithDividers extends StatelessWidget {
  final String text;
  final TextStyle textStyle;
  final Color dividerColor;
  final double dividerThickness;
  final double padding;

  const TextWithDividers({
    Key? key,
    required this.text,
    this.textStyle = const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
    this.dividerColor = Colors.black,
    this.dividerThickness = 2.0,
    this.padding = 8.0,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: Divider(
            color: dividerColor,
            thickness: dividerThickness,
          ),
        ),
        Padding(
          padding: EdgeInsets.symmetric(horizontal: padding),
          child: Text(
            text,
            style: textStyle,
          ),
        ),
        Expanded(
          child: Divider(
            color: dividerColor,
            thickness: dividerThickness,
          ),
        ),
      ],
    );
  }
}
